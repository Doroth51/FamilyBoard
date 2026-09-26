<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\FamilyGroupRepository;
use App\Repository\EnfantRepository;
use App\Repository\InvitationRepository;
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
        InvitationRepository $invitationRepo
    ) {
        return $this->render('admin/dashboard.html.twig', [
            'users' => $userRepo->findAll(),
            'groups' => $groupRepo->findAll(),
            'enfants' => $enfantRepo->findAll(),
            'invitations' => $invitationRepo->findAll(),
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
