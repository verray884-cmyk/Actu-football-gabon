<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Méthodes d'Authentification
    |--------------------------------------------------------------------------
    */

    /**
     * Afficher le formulaire d'inscription
     */
    public function showRegisterForm()
    {
        return view('inscription');
    }

    /**
     * Traiter l'inscription
     */
   public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nom' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:admins',
        'password' => 'required|string|min:6',
        'role' => 'required|in:editeur,admin,super_admin'
    ], [
        'nom.required' => 'Le nom est obligatoire',
        'email.required' => 'L\'email est obligatoire',
        'email.email' => 'L\'email doit être valide',
        'email.unique' => 'Cet email est déjà utilisé',
        'password.required' => 'Le mot de passe est obligatoire',
        'password.min' => 'Le mot de passe doit contenir au moins 6 caractères',
        'role.in' => 'Le rôle sélectionné n\'est pas valide'
    ]);

    if ($validator->fails()) {
        return back()
            ->withErrors($validator)
            ->withInput();
    }

    try {
        $admin = Admin::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        // Connecter automatiquement l'utilisateur après inscription
        auth()->guard('admin')->login($admin);

        // Rediriger directement vers le tableau de bord
        return redirect()->route('dashboard')->with('success', 'Inscription réussie ! Bienvenue sur le tableau de bord.');

    } catch (\Exception $e) {
        return back()->with('error', 'Erreur lors de la création du compte : ' . $e->getMessage());
    }
}

    /**
     * Afficher le formulaire de connexion
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'L\'email doit être valide',
            'password.required' => 'Le mot de passe est obligatoire'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (auth()->guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie!',
                'redirect' => route('dashboard')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email ou mot de passe incorrect'
        ], 401);
    }

    /**
     * Déconnexion
     */
    /**
 * Déconnexion
 */
public function logout(Request $request)
{
    auth()->guard('admin')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')
        ->with('success', 'Vous avez été déconnecté avec succès.');
}

    /*
    |--------------------------------------------------------------------------
    | Méthodes d'Administration
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $stats = [
            'total_admins' => Admin::count(),
            'super_admins' => Admin::where('role', 'super_admin')->count(),
            'admins' => Admin::where('role', 'admin')->count(),
            'editeurs' => Admin::where('role', 'editeur')->count(),
        ];

        return view('dashboard', compact('stats'));
    }

    public function listAdmins()
    {
        $admins = Admin::orderBy('created_at', 'desc')->paginate(15);
        return view('inscription', compact('admins'));
    }

    public function show($id)
    {
        $admin = Admin::findOrFail($id);

        if (!auth()->guard('admin')->user()->isSuperAdmin() &&
            auth()->guard('admin')->id() !== $admin->id) {
            abort(403, 'Accès non autorisé');
        }

        return view('inscription', compact('admin'));
    }

    public function edit($id)
    {
        $admin = Admin::findOrFail($id);

        if (!auth()->guard('admin')->user()->isSuperAdmin() &&
            auth()->guard('admin')->id() !== $admin->id) {
            abort(403, 'Accès non autorisé');
        }

        return view('inscription', compact('admin'));
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        if (!auth()->guard('admin')->user()->isSuperAdmin() &&
            auth()->guard('admin')->id() !== $admin->id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $rules = [
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $id,
        ];

        if (auth()->guard('admin')->user()->isSuperAdmin()) {
            $rules['role'] = 'required|in:editeur,admin,super_admin';
        }

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:6';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'nom' => $request->nom,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if (auth()->guard('admin')->user()->isSuperAdmin() && $request->filled('role')) {
                $data['role'] = $request->role;
            }

            $admin->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        if (!auth()->guard('admin')->user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $admin = Admin::findOrFail($id);

        if (auth()->guard('admin')->id() === $admin->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas supprimer votre propre compte'
            ], 403);
        }

        try {
            $admin->delete();

            return response()->json([
                'success' => true,
                'message' => 'Administrateur supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changeRole(Request $request, $id)
    {
        if (!auth()->guard('admin')->user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $admin = Admin::findOrFail($id);

        if (auth()->guard('admin')->id() === $admin->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas modifier votre propre rôle'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'role' => 'required|in:editeur,admin,super_admin'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $admin->update(['role' => $request->role]);

            return response()->json([
                'success' => true,
                'message' => 'Rôle modifié avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }

    public function settings()
    {
        if (!auth()->guard('admin')->user()->isSuperAdmin()) {
            abort(403, 'Accès réservé aux super administrateurs');
        }

        return view('inscription');
    }

}
