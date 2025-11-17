<?php

namespace App\Http\Controllers;

use App\Models\Classement;
use App\Models\Competition;
use App\Models\ResultatMatch; // ✅ Utiliser ResultatMatch au lieu de Match
use App\Models\Saison;
use App\Services\ClassementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // ✅ Import manquant corrigé
use Carbon\Carbon;

class ClassementController extends Controller
{
    protected $classementService;

    public function __construct(ClassementService $classementService)
    {
        $this->classementService = $classementService;
    }

    /**
     * Afficher la page des matchs avec classement
     */
    public function index(Request $request)
    {
        try {
            // 1️⃣ SAISON ACTIVE
            $saisonActive = $this->getSaisonActive();

            // 2️⃣ COMPÉTITIONS
            $competitions = Competition::all();
            $competitionPrincipale = $this->getCompetitionPrincipale($competitions);

            // 3️⃣ MATCHS EN DIRECT
            $matchsEnDirect = $this->getMatchsEnDirect();

            // 4️⃣ DERNIERS RÉSULTATS
            $matchsRecents = $this->getMatchsRecents();

            // 5️⃣ PROCHAINS MATCHS
            $prochainsMatchs = $this->getProchainsMatchs();

            // 6️⃣ CLASSEMENT
            $classement = $this->getClassement($saisonActive, $competitionPrincipale);

            // 7️⃣ Retourner la vue avec toutes les données
            return view('home.match', compact(
                'classement',
                'saisonActive',
                'matchsEnDirect',
                'matchsRecents',
                'prochainsMatchs',
                'competitions',
                'competitionPrincipale'
            ));

        } catch (\Exception $e) {
            Log::error('Erreur dans ClassementController::index : ' . $e->getMessage());
            Log::error('Stack trace : ' . $e->getTraceAsString());

            // En cas d’erreur, renvoyer une vue vide
            return view('home.match', [
                'classement' => collect(),
                'saisonActive' => null,
                'matchsEnDirect' => collect(),
                'matchsRecents' => collect(),
                'prochainsMatchs' => collect(),
                'competitions' => collect(),
                'competitionPrincipale' => null
            ])->with('error', 'Une erreur est survenue lors du chargement des données.');
        }
    }

    /**
     * Récupérer ou créer la saison active
     */
    private function getSaisonActive()
    {
        $saison = Saison::where('active', true)->first();

        if (!$saison) {
            $saison = Saison::orderBy('annee', 'desc')->first();
        }

        if (!$saison) {
            $anneeActuelle = date('Y');
            $saison = Saison::create([
                'nom' => "Saison {$anneeActuelle}-" . ($anneeActuelle + 1),
                'annee' => $anneeActuelle,
                'date_debut' => Carbon::create($anneeActuelle, 9, 1),
                'date_fin' => Carbon::create($anneeActuelle + 1, 6, 30),
                'active' => true
            ]);

            Log::info("✅ Saison créée automatiquement : {$saison->nom}");
        }

        return $saison;
    }

    /**
     * Récupérer la compétition principale
     */
    private function getCompetitionPrincipale($competitions)
    {
        $competition = Competition::where('nom', 'like', '%Championnat%')
            ->orWhere('nom', 'like', '%D1%')
            ->first();

        if (!$competition && $competitions->isNotEmpty()) {
            $competition = $competitions->first();
        }

        if (!$competition) {
            $competition = Competition::create([
                'nom' => 'Championnat National D1',
                'type' => 'championnat',
                'pays' => 'Gabon',
                'description' => 'Championnat national de première division du Gabon'
            ]);

            Log::info("✅ Compétition créée automatiquement : {$competition->nom}");
        }

        return $competition;
    }

    /**
     * Récupérer les matchs en direct
     */
    private function getMatchsEnDirect()
    {
        $now = Carbon::now();

        return ResultatMatch::where(function ($query) use ($now) {
                $query->where('statut', 'en_cours')
                    ->orWhere(function ($q) use ($now) {
                        $q->whereDate('date_match', $now->toDateString())
                          ->whereTime('date_match', '<=', $now->toTimeString())
                          ->whereNotIn('statut', ['termine', 'annule', 'reporte']);
                    });
            })
            ->with(['equipeDomicile', 'equipeExterieur', 'competition', 'saison'])
            ->orderBy('date_match', 'desc')
            ->get();
    }

    /**
     * Récupérer les derniers résultats
     */
    private function getMatchsRecents()
    {
        return ResultatMatch::where('statut', 'termine')
            ->with(['equipeDomicile', 'equipeExterieur', 'competition', 'saison'])
            ->orderBy('date_match', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Récupérer les prochains matchs
     */
    private function getProchainsMatchs()
    {
        $now = Carbon::now();

        return ResultatMatch::where(function ($query) use ($now) {
                $query->where('date_match', '>', $now)
                      ->whereNotIn('statut', ['termine', 'annule']);
            })
            ->with(['equipeDomicile', 'equipeExterieur', 'competition', 'saison'])
            ->orderBy('date_match', 'asc')
            ->limit(10)
            ->get();
    }

    /**
     * Récupérer le classement
     */
    private function getClassement($saison, $competition)
    {
        if (!$saison || !$competition) {
            return collect();
        }

        $classement = Classement::where('saison_id', $saison->id)
            ->where('competition_id', $competition->id)
            ->with('equipe')
            ->ordonne()
            ->get();

        // Si vide, recalcul automatique
        if ($classement->isEmpty()) {
            try {
                Log::info("🔁 Recalcul automatique du classement pour saison {$saison->id} / compétition {$competition->id}");

                $this->classementService->recalculerClassementComplet($competition->id, $saison->id);

                $classement = Classement::where('saison_id', $saison->id)
                    ->where('competition_id', $competition->id)
                    ->with('equipe')
                    ->ordonne()
                    ->get();

                if ($classement->isNotEmpty()) {
                    Log::info("✅ Classement recalculé avec succès ({$classement->count()} équipes)");
                }
            } catch (\Exception $e) {
                Log::error('❌ Erreur lors du recalcul automatique du classement : ' . $e->getMessage());
            }
        }

        return $classement;
    }

    /**
     * Recalculer le classement via AJAX
     */
    public function recalculer(Request $request)
    {
        $competitionId = $request->get('competition_id');
        $saisonId = $request->get('saison_id');

        if (!$competitionId || !$saisonId) {
            return response()->json([
                'success' => false,
                'message' => 'Compétition et saison requises'
            ], 400);
        }

        try {
            $this->classementService->recalculerClassementComplet($competitionId, $saisonId);

            return response()->json([
                'success' => true,
                'message' => 'Classement recalculé avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du recalcul manuel du classement : ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du recalcul : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher le classement d'une compétition spécifique
     */
    public function show(Request $request, $competitionId)
    {
        try {
            $competition = Competition::findOrFail($competitionId);
            $saisonActive = $this->getSaisonActive();

            $classement = Classement::where('saison_id', $saisonActive->id)
                ->where('competition_id', $competitionId)
                ->with('equipe')
                ->ordonne()
                ->get();

            return view('home.match', compact('classement', 'competition', 'saisonActive'));
        } catch (\Exception $e) {
            Log::error('Erreur dans show classement : ' . $e->getMessage());
            return back()->with('error', 'Impossible de charger le classement.');
        }
    }
}
