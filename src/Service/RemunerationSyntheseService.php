<?php

namespace App\Service;

use App\Entity\Enfant;
use App\Entity\Periode;
use App\Entity\Remuneration;

class RemunerationSyntheseService
{
    /**
     * Total des récompenses d’un enfant (toutes périodes)
     */
    public function totalEnfant(Enfant $enfant): float
    {
        $total = 0;

        foreach ($enfant->getRemunerations() as $rem) {
            $total += $rem->getMontant();
        }

        return $total;
    }

    /**
     * Total des récompenses d’un enfant pour une période donnée
     */
    public function totalEnfantParPeriode(Enfant $enfant, Periode $periode): float
    {
        $total = 0;

        foreach ($enfant->getRemunerations() as $rem) {
            if ($rem->getPeriode() === $periode) {
                $total += $rem->getMontant();
            }
        }

        return $total;
    }

    /**
     * Total par type de rémunération (ex : argent de poche, bonus…)
     */
    public function totalParType(Enfant $enfant): array
    {
        $result = [];

        foreach ($enfant->getRemunerations() as $rem) {
            $type = $rem->getType()->getName();

            if (!isset($result[$type])) {
                $result[$type] = 0;
            }

            $result[$type] += $rem->getMontant();
        }

        return $result;
    }

    /**
     * Synthèse complète pour un enfant
     */
    public function syntheseComplete(Enfant $enfant): array
    {
        return [
            'total' => $this->totalEnfant($enfant),
            'parType' => $this->totalParType($enfant),
        ];
    }
}
