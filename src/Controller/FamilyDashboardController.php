<?php

namespace App\Controller;

use App\Repository\EnfantRepository;
use App\Service\PeriodeSyntheseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_PARENT')]
#[Route('/family')]
class FamilyDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'family_dashboard')]
    public function dashboard(
        EnfantRepository $enfantRepo,
        PeriodeSyntheseService $syntheseService
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Récupérer les enfants du parent via les FamilyGroups
        $enfants = [];
        foreach ($user->getFamilyGroups() as $group) {
            foreach ($group->getEnfants() as $enfant) {
                $enfants[$enfant->getId()] = $enfant;
            }
        }

        // Synthèse par période pour chaque enfant
        $syntheses = [];
        foreach ($enfants as $enfant) {
            $syntheses[$enfant->getId()] = $syntheseService->syntheseComplete($enfant);
        }

        return $this->render('family/dashboard.html.twig', [
            'enfants' => $enfants,
            'syntheses' => $syntheses,
        ]);
    }
}
