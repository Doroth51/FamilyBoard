<?php

namespace App\Service;

use App\Entity\Note;

class CalculService
{
    public function moyenne(array $notes): ?float
    {
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
}
