<?php

namespace App\Controller;

use App\Repository\EnfantRepository;
use App\Repository\PeriodeRepository;
use App\Repository\NoteRepository;
use App\Repository\EvaluationRepository;
use App\Repository\RemunerationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard_parent')]
    public function parent(
        EnfantRepository $enfantRepo,
        PeriodeRepository $periodeRepo
    ): Response {
        return $this->render('dashboard/parent.html.twig', [
            'enfants' => $enfantRepo->findAll(),
            'periodes' => $periodeRepo->findAll(),
        ]);
    }

    #[Route('/enfant/{id}', name: 'dashboard_enfant')]
    public function enfant(
        int $id,
        EnfantRepository $enfantRepo,
        NoteRepository $noteRepo,
        EvaluationRepository $evalRepo,
        RemunerationRepository $remRepo
    ): Response {
        $enfant = $enfantRepo->find($id);

        return $this->render('dashboard/enfant.html.twig', [
            'enfant' => $enfant,
            'notes' => $noteRepo->findBy(['enfant' => $enfant]),
            'evaluations' => $evalRepo->findBy(['enfant' => $enfant]),
            'remunerations' => $remRepo->findBy(['enfant' => $enfant]),
        ]);
    }
}
