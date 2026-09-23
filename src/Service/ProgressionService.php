<?php

namespace App\Service;

use App\Entity\Enfant;
use App\Entity\Periode;
use App\Entity\Note;
use App\Entity\Evaluation;

class ProgressionService
{
    /**
     * Calcule la moyenne des notes pour une période donnée
     */
    public function moyenneParPeriode(Enfant $enfant, Periode $periode): ?float
    {
        $notes = [];

        foreach ($enfant->getNotes() as $note) {
            if ($note->getPeriode() === $periode) {
                $notes[] = $note;
            }
        }

        if (empty($notes)) {
            return null;
        }

        $total = 0;
        $coef = 0;

        foreach ($notes as $note) {
            $total += ($note->getNote() / $note->getDenominateur()) * $note->getCoefficient();
            $coef += $note->getCoefficient();
        }

        return $coef > 0 ? round($total / $coef, 2) : null;
    }

    /**
     * Convertit les appréciations en score numérique
     */
    public function scoreAppreciation(Evaluation $eval): int
    {
        $score = 0;
        switch ($eval->getNiveau()) {
            case 'TBM':
                $score = 4;
                break;
            case 'MS':
                $score = 3;
                break;
            case 'MI':
                $score = 2;
                break;
            case 'CNA':
                $score = 0;
                break;
            default:
                break;
        }
        return $score;
    }

    /**
     * Score moyen des appréciations pour une période
     */
    public function appreciationParPeriode(Enfant $enfant, Periode $periode): ?float
    {
        $scores = [];

        foreach ($enfant->getEvaluations() as $eval) {
            if ($eval->getPeriode() === $periode) {
                $scores[] = $this->scoreAppreciation($eval);
            }
        }

        if (empty($scores)) {
            return null;
        }

        return round(array_sum($scores) / count($scores), 2);
    }

    /**
     * Synthèse complète pour une période :
     * - moyenne des notes
     * - score des appréciations
     * - score global
     */
    public function progressionParPeriode(Enfant $enfant, Periode $periode): array
    {
        $moyenne = $this->moyenneParPeriode($enfant, $periode);
        $appreciation = $this->appreciationParPeriode($enfant, $periode);

        // Score global : combinaison notes + appréciations
        $global = null;

        if ($moyenne !== null && $appreciation !== null) {
            $global = round(($moyenne * 2 + $appreciation) / 3, 2);
        } elseif ($moyenne !== null) {
            $global = $moyenne;
        } elseif ($appreciation !== null) {
            $global = $appreciation;
        }

        return [
            'moyenne' => $moyenne,
            'appreciation' => $appreciation,
            'global' => $global,
        ];
    }

    /**
     * Progression complète sur toutes les périodes
     */
    public function progressionComplete(Enfant $enfant): array
    {
        $result = [];

        $periodesEnfant = $this->getPeriodesEnfant($enfant);
        foreach ($periodesEnfant as $periode) {
            $result[$periode->getId()] = [
                'periode' => $periode,
                'data' => $this->progressionParPeriode($enfant, $periode)
            ];
        }

        return $result;
    }

    private function getPeriodesEnfant(Enfant $enfant): array
    {
        $periodes = [];

        // Périodes via les notes
        foreach ($enfant->getNotes() as $note) {
            $periode = $note->getPeriode();
            if ($periode && !isset($periodes[$periode->getId()])) {
                $periodes[$periode->getId()] = $periode;
            }
        }

        // Périodes via les évaluations
        foreach ($enfant->getEvaluations() as $eval) {
            $periode = $eval->getPeriode();
            if ($periode && !isset($periodes[$periode->getId()])) {
                $periodes[$periode->getId()] = $periode;
            }
        }

        return array_values($periodes);
    }
}
