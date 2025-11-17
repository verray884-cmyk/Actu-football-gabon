<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Saison extends Model
{
    use HasFactory;

    protected $fillable = [
        'annee',
        'date_debut',
        'date_fin',
        'active',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'active' => 'boolean',
    ];


    /**
     * Relation avec les matchs (ou résultats de matchs)
     */
    public function matchs()
    {
        // ⚠️ Si ton modèle des matchs s'appelle "Match", remplace ResultatMatch::class par Match::class
        return $this->hasMany(ResultatMatch::class);
    }

    /**
     * Relation avec les classements
     */
    public function classements()
    {
        return $this->hasMany(Classement::class);
    }

    /**
     * Portée pour récupérer la saison active
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Attribut dynamique pour afficher le libellé de la saison
     * Exemple : "2024 / 2025"
     */
    public function getLabelAttribute()
    {
        if (!empty($this->annee)) {
            return $this->annee;
        }

        if ($this->date_debut instanceof Carbon && $this->date_fin instanceof Carbon) {
            return $this->date_debut->year . ' / ' . $this->date_fin->year;
        }

        return 'Saison inconnue';
    }
}
