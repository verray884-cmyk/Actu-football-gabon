<?php
// ==============================================
// ÉTAPE 1: Créer le Service ClassementService
// Fichier: app/Services/ClassementService.php
// ==============================================

namespace App\Services;

use App\Models\Classement;
use App\Models\ResultatMatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClassementService
{
    /**
     * Mettre à jour le classement après un résultat de match
     */
    public function mettreAJourClassement(ResultatMatch $match)
    {
        // Ne traiter que les matchs officiels et terminés
        if ($match->type_match !== 'officiel' || $match->statut !== 'termine') {
            Log::info("❌ Match ignoré (type: {$match->type_match}, statut: {$match->statut})");
            return;
        }

        // Vérifier que le match a une compétition
        if (!$match->competition) {
            Log::warning("⚠️ Match {$match->id} sans compétition");
            return;
        }

        // Ne pas traiter les matchs internationaux (CAN, Coupe du Monde)
        $competitionsExclues = ['can', 'coupe du monde', 'coupe d\'afrique', 'world cup', 'fifa'];
        $nomCompetition = strtolower($match->competition->nom ?? '');

        foreach ($competitionsExclues as $exclusion) {
            if (str_contains($nomCompetition, $exclusion)) {
                Log::info("❌ Compétition internationale ignorée: {$match->competition->nom}");
                return;
            }
        }

        try {
            DB::transaction(function () use ($match) {
                Log::info("🔄 Mise à jour du classement pour le match {$match->id}");

                // Mettre à jour pour l'équipe domicile
                $this->mettreAJourEquipe(
                    $match->equipe_domicile_id,
                    $match->competition_id,
                    $match->saison_id,
                    $match->buts_domicile ?? 0,
                    $match->buts_exterieur ?? 0
                );

                // Mettre à jour pour l'équipe extérieur
                $this->mettreAJourEquipe(
                    $match->equipe_exterieur_id,
                    $match->competition_id,
                    $match->saison_id,
                    $match->buts_exterieur ?? 0,
                    $match->buts_domicile ?? 0
                );

                // Recalculer les positions
                $this->recalculerPositions($match->competition_id, $match->saison_id);

                Log::info("✅ Classement mis à jour avec succès");
            });
        } catch (\Exception $e) {
            Log::error("❌ Erreur lors de la mise à jour du classement: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mettre à jour les stats d'une équipe
     */
    private function mettreAJourEquipe($equipeId, $competitionId, $saisonId, $butsPour, $butsContre)
    {
        $classement = Classement::firstOrCreate(
            [
                'equipe_id' => $equipeId,
                'competition_id' => $competitionId,
                'saison_id' => $saisonId,
            ],
            [
                'matches_joues' => 0,
                'victoires' => 0,
                'nuls' => 0,
                'defaites' => 0,
                'buts_pour' => 0,
                'buts_contre' => 0,
                'difference_buts' => 0,
                'points' => 0,
                'position' => 0,
            ]
        );

        // Incrémenter les matchs joués
        $classement->matches_joues++;

        // Ajouter les buts
        $classement->buts_pour += $butsPour;
        $classement->buts_contre += $butsContre;
        $classement->difference_buts = $classement->buts_pour - $classement->buts_contre;

        // Déterminer le résultat et les points
        if ($butsPour > $butsContre) {
            // Victoire
            $classement->victoires++;
            $classement->points += 3;
        } elseif ($butsPour === $butsContre) {
            // Nul
            $classement->nuls++;
            $classement->points += 1;
        } else {
            // Défaite
            $classement->defaites++;
        }

        $classement->last_updated = now();
        $classement->save();

        Log::info("📊 Stats équipe {$equipeId}: MJ={$classement->matches_joues}, PTS={$classement->points}");
    }

    /**
     * Recalculer les positions dans le classement
     */
    private function recalculerPositions($competitionId, $saisonId)
    {
        $classements = Classement::where('competition_id', $competitionId)
            ->where('saison_id', $saisonId)
            ->orderBy('points', 'desc')
            ->orderBy('difference_buts', 'desc')
            ->orderBy('buts_pour', 'desc')
            ->get();

        $position = 1;
        foreach ($classements as $classement) {
            $classement->position = $position++;
            $classement->save();
        }

        Log::info("📈 Positions recalculées: {$classements->count()} équipes");
    }

    /**
     * Recalculer tout le classement depuis zéro
     */
    public function recalculerClassementComplet($competitionId, $saisonId)
    {
        try {
            DB::transaction(function () use ($competitionId, $saisonId) {
                Log::info("🔄 Recalcul complet du classement: compétition={$competitionId}, saison={$saisonId}");

                // Réinitialiser tous les classements
                Classement::where('competition_id', $competitionId)
                    ->where('saison_id', $saisonId)
                    ->delete();

                // Récupérer tous les matchs officiels terminés
                $matchs = ResultatMatch::where('competition_id', $competitionId)
                    ->where('saison_id', $saisonId)
                    ->where('type_match', 'officiel')
                    ->where('statut', 'termine')
                    ->with('competition')
                    ->get();

                Log::info("📋 {$matchs->count()} matchs à traiter");

                // Filtrer les compétitions internationales
                $competitionsExclues = ['can', 'coupe du monde', 'coupe d\'afrique', 'world cup', 'fifa'];
                $matchsTraites = 0;

                foreach ($matchs as $match) {
                    $nomCompetition = strtolower($match->competition->nom ?? '');
                    $estExclu = false;

                    foreach ($competitionsExclues as $exclusion) {
                        if (str_contains($nomCompetition, $exclusion)) {
                            $estExclu = true;
                            break;
                        }
                    }

                    if (!$estExclu) {
                        $this->mettreAJourEquipe(
                            $match->equipe_domicile_id,
                            $competitionId,
                            $saisonId,
                            $match->buts_domicile ?? 0,
                            $match->buts_exterieur ?? 0
                        );

                        $this->mettreAJourEquipe(
                            $match->equipe_exterieur_id,
                            $competitionId,
                            $saisonId,
                            $match->buts_exterieur ?? 0,
                            $match->buts_domicile ?? 0
                        );

                        $matchsTraites++;
                    }
                }

                // Recalculer les positions
                $this->recalculerPositions($competitionId, $saisonId);

                Log::info("✅ Recalcul terminé: {$matchsTraites} matchs traités");
            });
        } catch (\Exception $e) {
            Log::error("❌ Erreur recalcul complet: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retirer un match du classement (en cas de suppression)
     */
    public function retirerMatch(ResultatMatch $match)
    {
        if ($match->type_match !== 'officiel' || $match->statut !== 'termine') {
            return;
        }

        // Vérifier si c'est une compétition exclue
        $competitionsExclues = ['can', 'coupe du monde', 'coupe d\'afrique', 'world cup', 'fifa'];
        $nomCompetition = strtolower($match->competition->nom ?? '');

        foreach ($competitionsExclues as $exclusion) {
            if (str_contains($nomCompetition, $exclusion)) {
                return;
            }
        }

        try {
            DB::transaction(function () use ($match) {
                Log::info("🗑️ Retrait du match {$match->id} du classement");

                // Retirer pour l'équipe domicile
                $this->retirerResultatEquipe(
                    $match->equipe_domicile_id,
                    $match->competition_id,
                    $match->saison_id,
                    $match->buts_domicile ?? 0,
                    $match->buts_exterieur ?? 0
                );

                // Retirer pour l'équipe extérieur
                $this->retirerResultatEquipe(
                    $match->equipe_exterieur_id,
                    $match->competition_id,
                    $match->saison_id,
                    $match->buts_exterieur ?? 0,
                    $match->buts_domicile ?? 0
                );

                // Recalculer les positions
                $this->recalculerPositions($match->competition_id, $match->saison_id);

                Log::info("✅ Match retiré du classement");
            });
        } catch (\Exception $e) {
            Log::error("❌ Erreur retrait match: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retirer les stats d'un match pour une équipe
     */
    private function retirerResultatEquipe($equipeId, $competitionId, $saisonId, $butsPour, $butsContre)
    {
        $classement = Classement::where('equipe_id', $equipeId)
            ->where('competition_id', $competitionId)
            ->where('saison_id', $saisonId)
            ->first();

        if (!$classement) {
            return;
        }

        // Décrémenter les matchs joués
        $classement->matches_joues = max(0, $classement->matches_joues - 1);

        // Retirer les buts
        $classement->buts_pour = max(0, $classement->buts_pour - $butsPour);
        $classement->buts_contre = max(0, $classement->buts_contre - $butsContre);
        $classement->difference_buts = $classement->buts_pour - $classement->buts_contre;

        // Retirer le résultat et les points
        if ($butsPour > $butsContre) {
            // Victoire
            $classement->victoires = max(0, $classement->victoires - 1);
            $classement->points = max(0, $classement->points - 3);
        } elseif ($butsPour === $butsContre) {
            // Nul
            $classement->nuls = max(0, $classement->nuls - 1);
            $classement->points = max(0, $classement->points - 1);
        } else {
            // Défaite
            $classement->defaites = max(0, $classement->defaites - 1);
        }

        $classement->last_updated = now();

        // Supprimer si plus de matchs
        if ($classement->matches_joues === 0) {
            $classement->delete();
            Log::info("🗑️ Classement équipe {$equipeId} supprimé (0 matchs)");
        } else {
            $classement->save();
            Log::info("📊 Stats équipe {$equipeId} mises à jour après retrait");
        }
    }
}
