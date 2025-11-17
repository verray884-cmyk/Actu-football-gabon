<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classement extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipe_id', 'competition_id', 'saison_id', 'matches_joues', 'victoires', 'nuls',
        'defaites', 'buts_pour', 'buts_contre', 'difference_buts', 'points', 'position', 'last_updated'
    ];
    protected $casts = ['last_updated' => 'datetime'];

    public function equipe()
    {
        return $this->belongsTo(Equipe::class);
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function saison()
    {
        return $this->belongsTo(Saison::class);
    }

    public function scopeOrdonne($query)
    {
        return $query->orderBy('points', 'desc')
                     ->orderBy('difference_buts', 'desc')
                     ->orderBy('buts_pour', 'desc');
    }
    
}
