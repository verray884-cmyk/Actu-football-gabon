<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'stade',
        'logo',
        'ville',
        'annee_creation',
        'description',
    ];

    protected $casts = [
        'annee_creation' => 'integer',
    ];
}
