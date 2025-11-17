<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actualite extends Model
{
    protected $fillable = [
        'admin_id',
        'titre',
        'contenu',
        'image',
        'categorie',
        'date_publication',
        'publie'
    ];

    protected $casts = [
        'date_publication' => 'datetime',
        'publie' => 'boolean'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Vérifier si le média est une vidéo
     */
    public function isVideo()
    {
        if (!$this->image) {
            return false;
        }

        $extension = strtolower(pathinfo($this->image, PATHINFO_EXTENSION));
        return in_array($extension, ['mp4', 'avi', 'mov', 'webm', 'mkv', 'flv']);
    }
}
