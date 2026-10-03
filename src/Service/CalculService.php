<?php

namespace App\Service;

use App\Entity\Enfant;

class CalculService
{
    public function calculMoyenneGenerale(Enfant $enfant): ?float
    {
        $notes = $enfant->getNotes();
        if (empty($notes)) {
            return null;
        }

        $totalNotes = 0;
        $totalBaremes = 0;
        $coefTotal = 0;
        $moyenne = 0;

        foreach ($notes as $note) {
            $coefTotal += $note->getCoefficient();
            $totalNotes += $note->getNote() * $note->getCoefficient();
            $totalBaremes += $note->getDenominateur() * $note->getCoefficient();
            $moyenne =  ($totalNotes / $totalBaremes) * 20;
        }

        return $coefTotal > 0 ? round($moyenne, 2) : null;
    }
}
