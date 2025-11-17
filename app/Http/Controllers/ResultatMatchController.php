<?php

namespace App\Http\Controllers;

use App\Models\ResultatMatch;
use App\Models\Equipe;
use App\Models\Competition;
use App\Models\Saison;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ClassementService;

class ResultatMatchController extends Controller
{
    protected $classementService;

    // Le constructeur injecte automatiquement le service
    public function __construct(ClassementService $classementService)
    {
        $this->classementService = $classementService;
    }

    /**
     * Affiche la page principale des matchs
     */
    public function index(Request $request)
    {
        try {
            $equipes = Equipe::orderBy('nom')->get();
            $competitions = Competition::orderBy('nom')->get();
            $saisons = Saison::orderBy('annee', 'desc')->get();

            $matchs = ResultatMatch::with(['equipeDomicile', 'equipeExterieur', 'competition', 'saison'])
                ->orderByDesc('date_match')
                ->paginate(20);

            return view('matchs', compact('equipes', 'competitions', 'saisons', 'matchs'));
        } catch (\Exception $e) {
            Log::error("Erreur index matchs : " . $e->getMessage());
            return back()->with('error', 'Erreur lors du chargement des matchs.');
        }
    }

    /**
     * Récupère tous les matchs (pour AJAX)
     */
    public function getAll(Request $request)
    {
        try {
            $query = ResultatMatch::with(['equipeDomicile', 'equipeExterieur', 'competition', 'saison']);

            if ($request->filled('equipe_id')) {
                $query->where(function ($q) use ($request) {
                    $q->where('equipe_domicile_id', $request->equipe_id)
                      ->orWhere('equipe_exterieur_id', $request->equipe_id);
                });
            }

            if ($request->filled('competition_id')) {
                $query->where('competition_id', $request->competition_id);
            }

            if ($request->filled('saison_id')) {
                $query->where('saison_id', $request->saison_id);
            }

            if ($request->filled('statut')) {
                $query->where('statut', $request->statut);
            }

            $matchs = $query->orderByDesc('date_match')
                ->get()
                ->map(function ($match) {
                    return [
                        'id' => $match->id,
                        'equipe_domicile' => $match->equipeDomicile->nom ?? 'N/A',
                        'equipe_exterieur' => $match->equipeExterieur->nom ?? 'N/A',
                        'logo_domicile' => $match->equipeDomicile?->logo ? asset('storage/' . $match->equipeDomicile->logo) : null,
                        'logo_exterieur' => $match->equipeExterieur?->logo ? asset('storage/' . $match->equipeExterieur->logo) : null,
                        'score_domicile' => $match->buts_domicile ?? 0,
                        'score_exterieur' => $match->buts_exterieur ?? 0,
                        'date_match' => $match->date_match ? $match->date_match->format('Y-m-d H:i') : null,
                        'competition' => $match->competition->nom ?? 'Sans compétition',
                        'lieu' => $match->lieu ?? 'Non précisé',
                        'statut' => $match->statut,
                        'type_match' => $match->type_match ?? 'officiel',
                    ];
                });

            return response()->json(['success' => true, 'data' => $matchs]);
        } catch (\Exception $e) {
            Log::error('Erreur getAll : ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors du chargement des matchs.'], 500);
        }
    }

    /**
     * Enregistre un nouveau match
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'equipe_domicile_id' => 'required|exists:equipes,id|different:equipe_exterieur_id',
                'equipe_exterieur_id' => 'required|exists:equipes,id',
                'competition_id' => 'nullable|exists:competitions,id',
                'saison_id' => 'required|exists:saisons,id',
                'buts_domicile' => 'required|integer|min:0|max:50',
                'buts_exterieur' => 'required|integer|min:0|max:50',
                'date_match' => 'required|date',
                'type_match' => 'nullable|in:officiel,amical',
                'statut' => 'required|in:termine,en_cours,reporte,annule',
                'lieu' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
            ]);

            $validated['compte_classement'] = ($validated['type_match'] ?? 'officiel') === 'officiel'
                                            && $validated['statut'] === 'termine';

            $match = ResultatMatch::create($validated);

            // ✅ AJOUT : Mettre à jour le classement automatiquement
            $this->classementService->mettreAJourClassement($match);

            // Charger les relations pour la réponse
            $match->load(['equipeDomicile', 'equipeExterieur', 'competition', 'saison']);

            // Retourner les données formatées pour JavaScript
            return response()->json([
                'success' => true,
                'message' => 'Match créé avec succès.',
                'data' => [
                    'id' => $match->id,
                    'competition' => $match->competition->nom ?? 'Sans compétition',
                    'competition_id' => $match->competition_id,
                    'saison_id' => $match->saison_id,
                    'date_match' => $match->date_match ? $match->date_match->format('Y-m-d H:i:s') : null,
                    'equipe_domicile' => $match->equipeDomicile->nom ?? 'N/A',
                    'equipe_domicile_id' => $match->equipe_domicile_id,
                    'logo_domicile' => $match->equipeDomicile->logo ? asset('storage/' . $match->equipeDomicile->logo) : null,
                    'score_domicile' => $match->buts_domicile ?? 0,
                    'equipe_exterieur' => $match->equipeExterieur->nom ?? 'N/A',
                    'equipe_exterieur_id' => $match->equipe_exterieur_id,
                    'logo_exterieur' => $match->equipeExterieur->logo ? asset('storage/' . $match->equipeExterieur->logo) : null,
                    'score_exterieur' => $match->buts_exterieur ?? 0,
                    'lieu' => $match->lieu ?? 'Non précisé',
                    'type_match' => $match->type_match ?? 'officiel',
                    'statut' => $match->statut,
                    'notes' => $match->notes,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur store match : ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la création du match.'], 500);
        }
    }

    /**
     * Affiche un match spécifique
     */
    public function show($id)
    {
        try {
            $match = ResultatMatch::with(['equipeDomicile', 'equipeExterieur', 'competition', 'saison'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $match->id,
                    'equipe_domicile_id' => $match->equipe_domicile_id,
                    'equipe_exterieur_id' => $match->equipe_exterieur_id,
                    'competition_id' => $match->competition_id,
                    'saison_id' => $match->saison_id,
                    'score_domicile' => $match->buts_domicile,
                    'score_exterieur' => $match->buts_exterieur,
                    'date_match' => $match->date_match?->format('Y-m-d\TH:i'),
                    'lieu' => $match->lieu,
                    'statut' => $match->statut,
                    'type_match' => $match->type_match ?? 'officiel',
                    'notes' => $match->notes,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur show match : ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Match non trouvé.'], 404);
        }
    }

    /**
     * Met à jour un match
     */
    public function update(Request $request, $id)
    {
        try {
            $match = ResultatMatch::findOrFail($id);

            // ✅ AJOUT : Sauvegarder l'ancien match avant modification
            $ancienMatch = clone $match;

            $validated = $request->validate([
                'equipe_domicile_id' => 'required|exists:equipes,id|different:equipe_exterieur_id',
                'equipe_exterieur_id' => 'required|exists:equipes,id',
                'competition_id' => 'nullable|exists:competitions,id',
                'saison_id' => 'required|exists:saisons,id',
                'buts_domicile' => 'required|integer|min:0|max:50',
                'buts_exterieur' => 'required|integer|min:0|max:50',
                'date_match' => 'required|date',
                'type_match' => 'nullable|in:officiel,amical',
                'statut' => 'required|in:termine,en_cours,reporte,annule',
                'lieu' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
            ]);

            $validated['compte_classement'] = ($validated['type_match'] ?? 'officiel') === 'officiel'
                                            && $validated['statut'] === 'termine';

            // ✅ AJOUT : Retirer l'ancien match du classement
            $this->classementService->retirerMatch($ancienMatch);

            $match->update($validated);

            // ✅ AJOUT : Ajouter le nouveau match au classement
            $this->classementService->mettreAJourClassement($match);

            // Charger les relations pour la réponse
            $match->load(['equipeDomicile', 'equipeExterieur', 'competition', 'saison']);

            // Retourner les données formatées pour JavaScript
            return response()->json([
                'success' => true,
                'message' => 'Match mis à jour avec succès.',
                'data' => [
                    'id' => $match->id,
                    'competition' => $match->competition->nom ?? 'Sans compétition',
                    'competition_id' => $match->competition_id,
                    'saison_id' => $match->saison_id,
                    'date_match' => $match->date_match ? $match->date_match->format('Y-m-d H:i:s') : null,
                    'equipe_domicile' => $match->equipeDomicile->nom ?? 'N/A',
                    'equipe_domicile_id' => $match->equipe_domicile_id,
                    'logo_domicile' => $match->equipeDomicile->logo ? asset('storage/' . $match->equipeDomicile->logo) : null,
                    'score_domicile' => $match->buts_domicile ?? 0,
                    'equipe_exterieur' => $match->equipeExterieur->nom ?? 'N/A',
                    'equipe_exterieur_id' => $match->equipe_exterieur_id,
                    'logo_exterieur' => $match->equipeExterieur->logo ? asset('storage/' . $match->equipeExterieur->logo) : null,
                    'score_exterieur' => $match->buts_exterieur ?? 0,
                    'lieu' => $match->lieu ?? 'Non précisé',
                    'type_match' => $match->type_match ?? 'officiel',
                    'statut' => $match->statut,
                    'notes' => $match->notes,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur update match : ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la modification.'], 500);
        }
    }

    /**
     * Supprime un match
     */
    public function destroy($id)
    {
        try {
            $match = ResultatMatch::findOrFail($id);

            // ✅ AJOUT : Retirer du classement avant suppression
            $this->classementService->retirerMatch($match);

            $match->delete();

            return response()->json(['success' => true, 'message' => 'Match supprimé avec succès.']);
        } catch (\Exception $e) {
            Log::error('Erreur destroy match : ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression.'], 500);
        }
    }

    /**
     * Statistiques globales
     */
    public function getStats()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'total' => ResultatMatch::count(),
                    'termines' => ResultatMatch::where('statut', 'termine')->count(),
                    'en_cours' => ResultatMatch::where('statut', 'en_cours')->count(),
                    'reportes' => ResultatMatch::where('statut', 'reporte')->count(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur getStats match : ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors du chargement des statistiques.'], 500);
        }
    }
    
}
