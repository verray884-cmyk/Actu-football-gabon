<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Admin extends Model implements AuthenticatableContract
{
    use HasFactory, Notifiable, Authenticatable;

    /**
     * Le nom de la table associée au modèle
     */
    protected $table = 'admins';

    /**
     * Les attributs qui peuvent être assignés en masse
     */
    protected $fillable = [
        'nom',
        'email',
        'password',
        'role',
    ];

    /**
     * Les attributs qui doivent être cachés pour la sérialisation
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs qui doivent être castés
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Les rôles disponibles
     */
    const ROLE_EDITEUR = 'editeur';
    const ROLE_ADMIN = 'admin';
    const ROLE_SUPER_ADMIN = 'super_admin';

    /**
     * Vérifier si l'admin a un rôle spécifique
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Vérifier si l'admin est un super admin
     */
    public function isSuperAdmin()
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Vérifier si l'admin est un admin ou super admin
     */
    public function isAdmin()
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN]);
    }

    /**
     * Vérifier si l'admin est un éditeur
     */
    public function isEditeur()
    {
        return $this->role === self::ROLE_EDITEUR;
    }

    /**
     * Obtenir le nom du rôle en français
     */
    public function getRoleNameAttribute()
    {
        return match($this->role) {
            self::ROLE_EDITEUR => 'Éditeur',
            self::ROLE_ADMIN => 'Administrateur',
            self::ROLE_SUPER_ADMIN => 'Super Administrateur',
            default => 'Inconnu'
        };
    }
}
