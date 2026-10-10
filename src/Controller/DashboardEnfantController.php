<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Enfant;
use App\Entity\Evaluation;
use App\Repository\EvaluationRepository;
use App\Repository\NoteRepository;
use App\Repository\PeriodeRepository;
use App\Repository\RemunerationRepository;
use App\Repository\ObjectifRepository;
use App\Service\CalculService;
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
        EvaluationRepository $evaluationRepo,
        RemunerationRepository $remRepo,
        ObjectifRepository $objRepo,
        CalculService $calculService
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

        $matieres = [];
        $moyenneGenerale = 0;

        if ($enfant->getEvaluationMode() === "note") {
            $matieres = [];
            $moyenneGenerale = $calculService->calculMoyenneGenerale($enfant);

            foreach ($enfant->getNotes() as $note) {
                $matiere = $note->getMatiere();
                if (!$matiere) {
                    continue;
                }

                $matiereId = $matiere->getId();
                if (!isset($matieres[$matiereId])) {
                    $matieres[$matiereId] = [
                        'id' => $matiereId,
                        'nom' => $matiere->getName(),
                        'notes' => [],
                        'totalPoints' => 0,
                        'totalCoefficients' => 0,
                    ];
                }

                $matieres[$matiereId]['notes'][] = $note;
                $matieres[$matiereId]['libelle'] = $note->getLibelle();
                $matieres[$matiereId]['totalPoints'] +=
                    ($note->getNote() / $note->getDenominateur()) * 20 * $note->getCoefficient();
                $matieres[$matiereId]['totalCoefficients'] +=
                    $note->getCoefficient();
            }
            foreach ($matieres as &$matiere) {
                $matiere['moyenne'] =
                    $matiere['totalCoefficients'] > 0
                    ? $matiere['totalPoints'] / $matiere['totalCoefficients']
                    : 0;
                unset(
                    $matiere['totalPoints'],
                    $matiere['totalCoefficients']
                );
            }
            // Retour attendu 
            // $matieres = [
            //      [
            //          'id' => 1,
            //          'nom' => 'Mathématiques',
            //          'moyenne' => 14.25,
            //          'notes' => [
            //              ['date' => '2026-09-01', 'note' => 15, 'coefficient' => 2],
            //              ['date' => '2026-09-15', 'note' => 13.5, 'coefficient' => 1],
            //          ]
            //      ],
            //      [
            //          'id' => 2,
            //          'nom' => 'Français',
            //          'moyenne' => 12.75,
            //          'notes' => [
            //              ['date' => '2026-09-03', 'note' => 11],
            //              ['date' => '2026-09-20', 'note' => 14.5],
            //          ]
            //      ]
            // ];
        } else { //evaluation
            $evaluations = $enfant->getEvaluations()->toArray();

            $scores = [];
            $evaluationsParMatiere = [];
            foreach ($evaluations as $evaluation) {
                $matiere = $evaluation->getMatiere()->getName();
                if (!isset($evaluationsParMatiere[$matiere])) {
                    $evaluationsParMatiere[$matiere] = [];
                }
                $evaluationsParMatiere[$matiere][] = $evaluation;
                $competence = $evaluation->getNiveau();
                $score = $this->getScore($competence);
                if (!is_null($score))
                    $scores[] = $score;
            }

            $moyenneGenerale = $this->getScoreMoyen($scores);
        }

        // Période en cours (ou dernière période)
        $periode = $periodeRepo->findCurrentOrLastForEnfant($enfant);

        // Notes de l'enfant
        $notes = $noteRepo->findBy(['enfant' => $enfant], ['date' => 'DESC']);
        $notesByMatieres = [];
        // if (!empty($notes)) {
        //     foreach ($notes as $note) {
        //         $notesByMatieres[$note['matiere']] = [];
        //     }
        // }
        // Rémunérations
        // $remunerations = $remRepo->findBy(['enfant' => $enfant], ['date' => 'DESC']);

        // // Objectifs
        // $objectifs = $objRepo->findBy(['enfant' => $enfant]);

        // // Préparation des données pour Chart.js — progression scolaire
        // $labels = [];
        // $progression = [];

        // foreach ($notes as $note) {
        //     $labels[] = $note->getDate()->format('d/m');
        //     $progression[] = round(($note->getNote() / $note->getDenominateur()) * 20, 2); // note sur 20
        // }

        // // Préparation des données pour Chart.js — récompenses
        // $rewardLabels = [];
        // $rewardValues = [];

        // foreach ($remunerations as $r) {
        //     $rewardLabels[] = $r->getDate()->format('d/m');
        //     $rewardValues[] = $r->getMontant();
        // }

        return $this->render('dashboard/enfant.html.twig', [
            'matieres' => $matieres,
            'moyenne' => $moyenneGenerale,
            'evaluationsParMatiere' => $evaluationsParMatiere ?? [],
            'enfant' => $enfant
        ]);
    }

    private function getScore(string $competence)
    {
        $score = null;
        switch ($competence) {
            case 'MI':
                $score = 1;
                break;
            case 'MF':
                $score = 2;
                break;
            case 'MS':
                $score = 3;
                break;
            case 'TBM':
                $score = 4;
                break;
            default:
                break;
        }
        return $score;
    }

    private function getScoreMoyen($scores)
    {
        $nbCompetences = count($scores);
        $total = 0;
        $moyenne = null;
        if (!empty($scores)) {
            foreach ($scores as $score) {
                $total += $score;
            }

            $moyenne = $total / $nbCompetences;
        }

        return $moyenne;
    }
}
