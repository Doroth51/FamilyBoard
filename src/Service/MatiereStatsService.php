<?php

namespace App\Service;

use App\Entity\Enfant;

class MatiereStatsService
{
    public function moyennesParMatiere(Enfant $enfant): array
    {
        $stats = [];

        foreach ($enfant->getNotes() as $note) {
            $matiere = $note->getMatiere();

            if (!isset($stats[$matiere])) {
                $stats[$matiere] = [
                    'total' => 0,
                    'coef' => 0
                ];
            }

            $stats[$matiere]['total'] += ($note->getNote() / $note->getDenominateur()) * $note->getCoefficient();
            $stats[$matiere]['coef'] += $note->getCoefficient();
        }

        // Calcul des moyennes
        $result = [];
        foreach ($stats as $matiere => $data) {
            $result[$matiere] = $data['coef'] > 0 ? round($data['total'] / $data['coef'], 2) : 0;
        }

        return $result;
    }
}
