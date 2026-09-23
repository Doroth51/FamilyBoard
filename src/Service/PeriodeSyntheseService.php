<?php

namespace App\Service;

use App\Entity\Enfant;
use App\Entity\Periode;
use App\Service\ProgressionService;
use App\Service\RemunerationSyntheseService;

class PeriodeSyntheseService
{
    private ProgressionService $progressionService;
    private RemunerationSyntheseService $remunerationSyntheseService;

    public function __construct(
        ProgressionService $progressionService,
        RemunerationSyntheseService $remunerationSyntheseService
    ) {
        $this->progressionService = $progressionService;
        $this->remunerationSyntheseService = $remunerationSyntheseService;
    }

    public function syntheseParPeriode(Enfant $enfant, Periode $periode): array
    {
        $progression = $this->progressionService->progressionParPeriode($enfant, $periode);
        $recompenses = $this->remunerationSyntheseService->totalEnfantParPeriode($enfant, $periode);

        return [
            'periode' => $periode,
            'moyenne' => $progression['moyenne'],
            'appreciation' => $progression['appreciation'],
            'global' => $progression['global'],
            'recompenses' => $recompenses,
        ];
    }

    public function syntheseComplete(Enfant $enfant): array
    {
        $periodes = [];

        // Récupération des périodes via notes + évaluations
        foreach ($enfant->getNotes() as $note) {
            $periodes[$note->getPeriode()->getId()] = $note->getPeriode();
        }
        foreach ($enfant->getEvaluations() as $eval) {
            $periodes[$eval->getPeriode()->getId()] = $eval->getPeriode();
        }

        $result = [];
        foreach ($periodes as $periode) {
            $result[$periode->getId()] = $this->syntheseParPeriode($enfant, $periode);
        }

        return $result;
    }
}
