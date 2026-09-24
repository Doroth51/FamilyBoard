<?php

namespace App\Controller;

use App\Entity\Enfant;
use App\Repository\NoteRepository;
use App\Repository\EvaluationRepository;
use App\Repository\RemunerationRepository;
use App\Service\PeriodeSyntheseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_PARENT')]
#[Route('/dashboard/enfant')]
class DashboardEnfantController extends AbstractController
{
    #[Route('/{id}', name: 'dashboard_enfant')]
    public function enfant(
        Enfant $enfant,
        NoteRepository $noteRepo,
        EvaluationRepository $evalRepo,
        RemunerationRepository $remRepo,
        PeriodeSyntheseService $syntheseService
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Vérification que l’enfant appartient au parent
        $allowed = false;
        foreach ($user->getFamilyGroups() as $group) {
            if ($group->getEnfants()->contains($enfant)) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            throw $this->createAccessDeniedException("Cet enfant n'appartient pas à votre famille.");
        }

        // Données
        $notes = $noteRepo->findBy(['enfant' => $enfant]);
        $evaluations = $evalRepo->findBy(['enfant' => $enfant]);
        $recompenses = $remRepo->findBy(['enfant' => $enfant]);

        // Synthèse par période
        $synthese = $syntheseService->syntheseComplete($enfant);

        return $this->render('dashboard/enfant.html.twig', [
            'enfant' => $enfant,
            'notes' => $notes,
            'evaluations' => $evaluations,
            'recompenses' => $recompenses,
            'synthese' => $synthese,
        ]);
    }
}
