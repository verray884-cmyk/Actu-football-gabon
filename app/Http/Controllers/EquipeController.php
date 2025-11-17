<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EquipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->wantsJson() || request()->ajax()) {
            try {
                $equipes = Equipe::orderBy('nom', 'asc')->get();

                $equipes = $equipes->map(function($equipe) {
                    return [
                        'id' => $equipe->id,
                        'nom' => $equipe->nom,
                        'stade' => $equipe->stade,
                        'logo' => $equipe->logo ? asset('storage/' . $equipe->logo) : null,
                        'ville' => $equipe->ville,
                        'fondation' => $equipe->fondation,
                        'entraineur' => $equipe->entraineur,
                        'description' => $equipe->description,
                        'created_at' => $equipe->created_at,
                        'updated_at' => $equipe->updated_at,
                    ];
                });

                return response()->json([
                    'success' => true,
                    'data' => $equipes
                ]);
            } catch (\Throwable $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors du chargement des équipes',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        $equipes = Equipe::orderBy('nom', 'asc')->get();
        return view('equipes', compact('equipes'));
    }

    public function create()
    {
        return view('equipes');
    }

    public function store(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255|unique:equipes,nom',
                'stade' => 'nullable|string|max:255',
                'ville' => 'nullable|string|max:255',
                'fondation' => 'nullable|integer|min:1900|max:' . date('Y'),
                'entraineur' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            try {
                $data = $request->all();

                if ($request->hasFile('logo')) {
                    $logo = $request->file('logo');
                    $logoName = time() . '_' . uniqid() . '.' . $logo->getClientOriginalExtension();
                    $logoPath = $logo->storeAs('equipes/logos', $logoName, 'public');
                    $data['logo'] = $logoPath;
                }

                $equipe = Equipe::create($data);

                return response()->json([
                    'success' => true,
                    'message' => 'Équipe créée avec succès',
                    'data' => $equipe
                ], 201);
            } catch (\Throwable $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:equipes,nom',
            'stade' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'fondation' => 'nullable|integer|min:1900|max:' . date('Y'),
            'entraineur' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_' . uniqid() . '.' . $logo->getClientOriginalExtension();
            $logoPath = $logo->storeAs('equipes/logos', $logoName, 'public');
            $validated['logo'] = $logoPath;
        }

        Equipe::create($validated);

        return redirect()->route('equipes.index')->with('success', 'Équipe créée avec succès !');
    }

    public function show($id)
    {
        if (request()->wantsJson() || request()->ajax()) {
            try {
                $equipe = Equipe::findOrFail($id);

                return response()->json([
                    'success' => true,
                    'data' => $equipe
                ]);
            } catch (\Throwable $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Équipe non trouvée'
                ], 404);
            }
        }

        $equipe = Equipe::findOrFail($id);
        return view('equipes', compact('equipe'));
    }

    public function edit($id)
    {
        $equipe = Equipe::findOrFail($id);
        return view('equipes', compact('equipe'));
    }

    public function update(Request $request, $id)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255|unique:equipes,nom,' . $id,
                'stade' => 'nullable|string|max:255',
                'ville' => 'nullable|string|max:255',
                'fondation' => 'nullable|integer|min:1900|max:' . date('Y'),
                'entraineur' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            try {
                $equipe = Equipe::findOrFail($id);
                $data = $request->all();

                if ($request->hasFile('logo')) {
                    if ($equipe->logo && Storage::disk('public')->exists($equipe->logo)) {
                        Storage::disk('public')->delete($equipe->logo);
                    }

                    $logo = $request->file('logo');
                    $logoName = time() . '_' . uniqid() . '.' . $logo->getClientOriginalExtension();
                    $logoPath = $logo->storeAs('equipes/logos', $logoName, 'public');
                    $data['logo'] = $logoPath;
                }

                $equipe->update($data);

                return response()->json([
                    'success' => true,
                    'message' => 'Équipe modifiée avec succès',
                    'data' => $equipe
                ]);
            } catch (\Throwable $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la modification',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:equipes,nom,' . $id,
            'stade' => 'nullable|string|max:255',
            'ville' => 'nullable|string|max:255',
            'fondation' => 'nullable|integer|min:1900|max:' . date('Y'),
            'entraineur' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $equipe = Equipe::findOrFail($id);

        if ($request->hasFile('logo')) {
            if ($equipe->logo && Storage::disk('public')->exists($equipe->logo)) {
                Storage::disk('public')->delete($equipe->logo);
            }

            $logo = $request->file('logo');
            $logoName = time() . '_' . uniqid() . '.' . $logo->getClientOriginalExtension();
            $logoPath = $logo->storeAs('equipes/logos', $logoName, 'public');
            $validated['logo'] = $logoPath;
        }

        $equipe->update($validated);

        return redirect()->route('equipes.index')->with('success', 'Équipe modifiée avec succès !');
    }

    public function destroy($id)
    {
        if (request()->wantsJson() || request()->ajax()) {
            try {
                $equipe = Equipe::findOrFail($id);

                if ($equipe->logo && Storage::disk('public')->exists($equipe->logo)) {
                    Storage::disk('public')->delete($equipe->logo);
                }

                $equipe->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Équipe supprimée avec succès'
                ]);
            } catch (\Throwable $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        $equipe = Equipe::findOrFail($id);

        if ($equipe->logo && Storage::disk('public')->exists($equipe->logo)) {
            Storage::disk('public')->delete($equipe->logo);
        }

        $equipe->delete();

        return redirect()->route('equipes.index')->with('success', 'Équipe supprimée avec succès !');
    }

    public function getStats()
    {
        try {
            $total = Equipe::count();
            $villes = Equipe::distinct('ville')->whereNotNull('ville')->count('ville');
            $currentYear = date('Y');

            $moyenneAge = 0;
            if ($total > 0) {
                $equipes = Equipe::whereNotNull('fondation')->get();
                if ($equipes->count() > 0) {
                    $sumAge = $equipes->sum(fn($equipe) => $currentYear - $equipe->fondation);
                    $moyenneAge = round($sumAge / $equipes->count());
                }
            }

            $plusAncienneEquipe = Equipe::whereNotNull('fondation')->orderBy('fondation', 'asc')->first();
            $plusAncienne = $plusAncienneEquipe ? $plusAncienneEquipe->nom : 'N/A';

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'villes' => $villes,
                    'moyenne_age' => $moyenneAge,
                    'plus_ancienne' => $plusAncienne
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
