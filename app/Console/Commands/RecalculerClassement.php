<?php

namespace App\Console\Commands;

use App\Services\ClassementService;
use App\Models\Competition;
use App\Models\Saison;
use Illuminate\Console\Command;

class RecalculerClassement extends Command
{
    protected $signature = 'classement:recalculer {competition_id?} {saison_id?}';
    protected $description = 'Recalculer le classement pour une compétition et une saison';

    protected $classementService;

    public function __construct(ClassementService $classementService)
    {
        parent::__construct();
        $this->classementService = $classementService;
    }

    public function handle()
    {
        $this->info('🏆 Recalcul du classement');
        $this->info('─────────────────────────');

        $competitionId = $this->argument('competition_id');
        $saisonId = $this->argument('saison_id');

        // Si pas de compétition spécifiée, demander
        if (!$competitionId) {
            $competitions = Competition::all();

            if ($competitions->isEmpty()) {
                $this->error('❌ Aucune compétition trouvée');
                return 1;
            }

            $choices = $competitions->pluck('nom', 'id')->toArray();
            $competitionId = $this->choice('Choisissez une compétition', $choices);
        }

        // Si pas de saison spécifiée, demander
        if (!$saisonId) {
            $saisons = Saison::orderBy('annee', 'desc')->get();

            if ($saisons->isEmpty()) {
                $this->error('❌ Aucune saison trouvée');
                return 1;
            }

            $choices = $saisons->pluck('annee', 'id')->toArray();
            $saisonId = $this->choice('Choisissez une saison', $choices);
        }

        $competition = Competition::find($competitionId);
        $saison = Saison::find($saisonId);

        if (!$competition || !$saison) {
            $this->error('❌ Compétition ou saison invalide');
            return 1;
        }

        $this->info("📋 Compétition: {$competition->nom}");
        $this->info("📅 Saison: {$saison->annee}");
        $this->newLine();

        if (!$this->confirm('Confirmer le recalcul ?')) {
            $this->info('⏹️ Annulé');
            return 0;
        }

        $this->info('🔄 Recalcul en cours...');

        try {
            $this->classementService->recalculerClassementComplet($competitionId, $saisonId);
            $this->newLine();
            $this->info('✅ Classement recalculé avec succès!');
            return 0;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Erreur: ' . $e->getMessage());
            return 1;
        }
    }
}
