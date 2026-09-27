<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\EnfantRepository;
use App\Repository\PeriodeRepository;
use App\Repository\NoteRepository;
use App\Repository\EvaluationRepository;
use App\Repository\FamilyGroupRepository;
use App\Repository\InvitationRepository;
use App\Repository\RemunerationRepository;
use App\Service\MatiereStatsService;
use App\Service\PeriodeSyntheseService;
use App\Service\ProgressionService;
use App\Service\RemunerationSyntheseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_PARENT')]
#[Route('/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard_parent')]
    public function parent(
        EnfantRepository $enfantRepo,
        PeriodeRepository $periodeRepo,
        NoteRepository $noteRepo,
        PeriodeSyntheseService $periodeSyntheseService,
        InvitationRepository $invitationRepository,
        FamilyGroupRepository $familyGroupRepository
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $enfants = $enfantRepo->findAll();
        $invitations = $invitationRepository->findAll();
        $periodes = $periodeRepo->findAll();
        $groupEntity = $user->getFamilyGroups()->first();
        $group = [
            'id' => $groupEntity->getId(),
            'name' => $groupEntity->getName(),
            'enfants' => $groupEntity->getEnfants(),
            'users' => $groupEntity->getUsers(),
            'invitations' => $groupEntity->getInvitations()
        ];
        $syntheses = [];

        foreach ($enfants as $enfant) {
            $syntheses[$enfant->getId()] = $periodeSyntheseService->syntheseComplete($enfant);
        }

        return $this->render('dashboard/parent.html.twig', [
            'enfants' => $enfants,
            'notes' => $noteRepo,
            'syntheses' => $syntheses,
            'periodes' => $periodes,
            'invitations' => $invitations,
            'group' => $group
        ]);
    }

    #[Route('/enfant/{id}', name: 'dashboard_enfant')]
    public function enfant(
        int $id,
        EnfantRepository $enfantRepo,
        NoteRepository $noteRepo,
        EvaluationRepository $evalRepo,
        RemunerationRepository $remRepo,
        RemunerationSyntheseService $syntheseService,
        ProgressionService $progressionService,
        MatiereStatsService $matiereStatsService
    ): Response {
        $enfant = $enfantRepo->find($id);

        return $this->render('dashboard/enfant.html.twig', [
            'enfant' => $enfant,
            'notes' => $noteRepo->findBy(['enfant' => $enfant]),
            'evaluations' => $evalRepo->findBy(['enfant' => $enfant]),
            'remunerations' => $remRepo->findBy(['enfant' => $enfant]),
            'synthese' => $syntheseService->syntheseComplete($enfant),
            'progression' => $progressionService->progressionComplete($enfant),
            'matiereStats' => $matiereStatsService->moyennesParMatiere($enfant),
        ]);
    }
}
