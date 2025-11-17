<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ResultatMatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'equipe_domicile_id',
        'equipe_exterieur_id',
        'competition_id',
        'saison_id',
        'buts_domicile',
        'buts_exterieur',
        'date_match',
        'type_match',
        'statut',
        'lieu',
        'notes',
        'compte_classement'
    ];

    protected $casts = [
        // ✅ Corrigé pour éviter l’erreur "date::format()"
        'date_match' => 'datetime',
        'compte_classement' => 'boolean',
    ];

    /**
     * Relations
     */
    public function equipeDomicile()
    {
        return $this->belongsTo(Equipe::class, 'equipe_domicile_id');
    }

    public function equipeExterieur()
    {
        return $this->belongsTo(Equipe::class, 'equipe_exterieur_id');
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function saison()
    {
        return $this->belongsTo(Saison::class);
    }

    /**
     * Accesseurs et méthodes utilitaires
     */
    public function getResultatAttribute()
    {
        if ($this->buts_domicile > $this->buts_exterieur) {
            return 'Victoire ' . $this->equipeDomicile->nom;
        } elseif ($this->buts_domicile < $this->buts_exterieur) {
            return 'Victoire ' . $this->equipeExterieur->nom;
        } else {
            return 'Match nul';
        }
    }

    public function getScoreAttribute()
    {
        return $this->buts_domicile . ' - ' . $this->buts_exterieur;
    }

    /**
     * ✅ Affichage formaté de la date en français
     */
    public function getDateFormateeAttribute()
    {
        if (!$this->date_match) {
            return null;
        }

        return $this->date_match
            ->locale('fr')
            ->isoFormat('dddd D MMMM YYYY à HH[h]mm');
    }

    /**
     * Retourne le résultat pour une équipe spécifique
     */
    public function getResultatPourEquipe($equipeId)
    {
        $isDomicile = $this->equipe_domicile_id == $equipeId;
        $butsMarques = $isDomicile ? $this->buts_domicile : $this->buts_exterieur;
        $butsEncaisses = $isDomicile ? $this->buts_exterieur : $this->buts_domicile;

        if ($butsMarques > $butsEncaisses) {
            return 'victoire';
        } elseif ($butsMarques < $butsEncaisses) {
            return 'defaite';
        } else {
            return 'nul';
        }
    }

    /**
     * Retourne le badge de statut
     */
    public function getStatutBadgeAttribute()
    {
        return match($this->statut) {
            'termine' => '<span class="badge bg-success">Terminé</span>',
            'en_cours' => '<span class="badge bg-primary">En cours</span>',
            'reporte' => '<span class="badge bg-warning">Reporté</span>',
            'annule' => '<span class="badge bg-danger">Annulé</span>',
            default => '<span class="badge bg-secondary">-</span>',
        };
    }

    /**
     * Retourne le badge de type de match
     */
    public function getTypeMatchBadgeAttribute()
    {
        return match($this->type_match) {
            'officiel' => '<span class="badge bg-info">Officiel</span>',
            'amical' => '<span class="badge bg-secondary">Amical</span>',
            default => '<span class="badge bg-light text-dark">-</span>',
        };
    }

    /**
     * Scopes
     */
    public function scopeTermine($query)
    {
        return $query->where('statut', 'termine');
    }

    public function scopeOfficiel($query)
    {
        return $query->where('type_match', 'officiel');
    }

    public function scopePourClassement($query)
    {
        return $query->where('compte_classement', true);
    }

    public function scopeDeSaison($query, $saisonId)
    {
        return $query->where('saison_id', $saisonId);
    }

    public function scopeDeCompetition($query, $competitionId)
    {
        return $query->where('competition_id', $competitionId);
    }

    public function scopeDeEquipe($query, $equipeId)
    {
        return $query->where(function ($q) use ($equipeId) {
            $q->where('equipe_domicile_id', $equipeId)
              ->orWhere('equipe_exterieur_id', $equipeId);
        });
    }
}
