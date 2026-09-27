<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Enfant;
use App\Repository\NoteRepository;
use App\Repository\PeriodeRepository;
use App\Repository\RemunerationRepository;
use App\Repository\ObjectifRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardEnfantController extends AbstractController
{
    #[Route('/dashboard/enfant/{id}', name: 'dashboard_enfant')]
    public function index(
        Enfant $enfant,
        NoteRepository $noteRepo,
        PeriodeRepository $periodeRepo,
        RemunerationRepository $remRepo,
        ObjectifRepository $objRepo
    ): Response {

        /** @var User $user */
        // Vérification : le parent connecté doit être lié à cet enfant
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException("Accès refusé.");
        }

        // Cas 1 : l'utilisateur est un enfant
        if (in_array('ROLE_ENFANT', $user->getRoles(), true)) {
            if ($user->getEnfants()->count() === 0 || !$user->getEnfants()->contains($enfant)) {
                throw $this->createAccessDeniedException("Accès refusé.");
            }
        }

        // Cas 2 : l'utilisateur est un parent
        if (in_array('ROLE_PARENT', $user->getRoles(), true)) {

            $userGroups = $user->getFamilyGroups();
            $enfantGroups = $enfant->getFamilyGroups();

            $hasCommonGroup = false;

            foreach ($userGroups as $group) {
                if ($enfantGroups->contains($group)) {
                    $hasCommonGroup = true;
                    break;
                }
            }

            if (!$hasCommonGroup) {
                throw $this->createAccessDeniedException("Accès refusé.");
            }
        }

        // Période en cours (ou dernière période)
        $periode = $periodeRepo->findCurrentOrLastForEnfant($enfant);

        // Notes de l'enfant
        $notes = $noteRepo->findBy(['enfant' => $enfant], ['date' => 'DESC']);

        // Rémunérations
        $remunerations = $remRepo->findBy(['enfant' => $enfant], ['date' => 'DESC']);

        // Objectifs
        $objectifs = $objRepo->findBy(['enfant' => $enfant]);

        // Préparation des données pour Chart.js — progression scolaire
        $labels = [];
        $progression = [];

        foreach ($notes as $note) {
            $labels[] = $note->getDate()->format('d/m');
            $progression[] = round(($note->getNote() / $note->getDenominateur()) * 20, 2); // note sur 20
        }

        // Préparation des données pour Chart.js — récompenses
        $rewardLabels = [];
        $rewardValues = [];

        foreach ($remunerations as $r) {
            $rewardLabels[] = $r->getDate()->format('d/m');
            $rewardValues[] = $r->getMontant();
        }

        return $this->render('dashboard/enfant.html.twig', [
            'enfant' => $enfant,
            'periode' => $periode,
            'notes' => $notes,
            'remunerations' => $remunerations,
            'objectifs' => $objectifs,
            'labels' => $labels,
            'progression' => $progression,
            'rewardLabels' => $rewardLabels,
            'rewardValues' => $rewardValues,
        ]);
    }
}
