<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resultat_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipe_domicile_id')->constrained('equipes')->onDelete('cascade');
            $table->foreignId('equipe_exterieur_id')->constrained('equipes')->onDelete('cascade');
            $table->foreignId('competition_id')->nullable()->constrained('competitions')->onDelete('set null');
            $table->foreignId('saison_id')->constrained('saisons')->onDelete('cascade');
            $table->integer('buts_domicile')->default(0);
            $table->integer('buts_exterieur')->default(0);
            $table->date('date_match');
            $table->enum('type_match', ['officiel', 'amical'])->default('officiel');
            $table->enum('statut', ['termine', 'en_cours', 'reporte', 'annule'])->default('termine');
            $table->string('lieu')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('compte_classement')->default(true);
            $table->softDeletes();
            $table->timestamps();

            // Index pour améliorer les performances
            $table->index('date_match');
            $table->index('statut');
            $table->index('type_match');
            $table->index(['equipe_domicile_id', 'equipe_exterieur_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultat_matches');
    }
};
