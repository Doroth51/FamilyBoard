<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\FamilyGroupRepository;
use App\Repository\EnfantRepository;
use App\Repository\InvitationRepository;
use App\Repository\ClasseRepository;
use App\Repository\MatiereRepository;
use App\Repository\PeriodeRepository;
use App\Repository\RemunerationTypeRepository;
use App\Repository\NiveauRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(
        UserRepository $userRepo,
        FamilyGroupRepository $groupRepo,
        EnfantRepository $enfantRepo,
        InvitationRepository $invitationRepo,
        ClasseRepository $classeRepository,
        MatiereRepository $matiereRepository,
        PeriodeRepository $periodeRepository,
        RemunerationTypeRepository $remunerationTypeRepository,
        NiveauRepository $niveauRepository
    ) {
        return $this->render('admin/dashboard.html.twig', [
            'users' => $userRepo->findAll(),
            'groups' => $groupRepo->findAll(),
            'enfants' => $enfantRepo->findAll(),
            'invitations' => $invitationRepo->findAll(),
            'classes' => $classeRepository->findAll(),
            'matieres' => $matiereRepository->findAll(),
            'periodes' => $periodeRepository->findAll(),
            'remuneration_types' => $remunerationTypeRepository->findAll(),
            'niveaux' => $niveauRepository->findAll(),
        ]);
    }

    #[Route('/admin/invitations', name: 'admin_invitations')]
    public function adminInvitations(InvitationRepository $repo)
    {
        return $this->render('admin/invitations.html.twig', [
            'invitations' => $repo->findAll()
        ]);
    }
}
