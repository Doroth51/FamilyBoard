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
use Symfony\Component\Finder\Finder;
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
        } else { //evaluation
            $evaluations = $enfant->getEvaluations()->toArray();
            $scores = [];
            $evaluationsParMatiere = [];

            foreach ($evaluations as $evaluation) {
                $nomMatiere = $evaluation->getMatiere()->getName();

                if (!isset($evaluationsParMatiere[$nomMatiere])) {
                    $evaluationsParMatiere[$nomMatiere] = [
                        'evaluations' => [],
                        'scoreMoyen' => 0,
                    ];
                }

                $evaluationsParMatiere[$nomMatiere]['evaluations'][] = $evaluation;

                $score = $this->getScore($evaluation->getNiveau());

                if (!is_null($score)) {
                    $scores[] = $score;
                }
            }

            foreach ($evaluationsParMatiere as $nomMatiere => $data) {

                $scoresMatiere = [];

                foreach ($data['evaluations'] as $evaluation) {
                    $score = $this->getScore($evaluation->getNiveau());

                    if (!is_null($score)) {
                        $scoresMatiere[] = $score;
                    }
                }

                $evaluationsParMatiere[$nomMatiere]['scoreMoyen']
                    = $this->getScoreMoyen($scoresMatiere);
            }

            $moyenneGenerale = $this->getScoreMoyen($scores);
        }

        // Période en cours (ou dernière période)
        //$periode = $periodeRepo->findCurrentOrLastForEnfant($enfant);

        // Notes de l'enfant
        // $notes = $noteRepo->findBy(['enfant' => $enfant], ['date' => 'DESC']);
        // $notesByMatieres = [];
        // if (!empty($notes)) {
        //     foreach ($notes as $note) {
        //         $notesByMatieres[$note['matiere']] = [];
        //     }
        // }
        // Rémunérations
        $remunerations = $remRepo->findBy(['enfant' => $enfant], ['date' => 'DESC']);
        $totauxParType = [];
        $remunerationsParType = [];

        foreach ($remunerations as $remuneration) {
            $type = $remuneration->getType()->getName();

            if (!isset($remunerationsParType[$type])) {
                $remunerationsParType[$type] = [
                    'remunerations' => [],
                    'montantTotal' => 0,
                ];
            }

            $remunerationsParType[$type]['remunerations'][] = $remuneration;
            $remunerationsParType[$type]['montantTotal'] += $remuneration->getMontant();

            if (!isset($totauxParType[$type])) {
                $totauxParType[$type] = [
                    'nombre' => 0,
                    'total' => 0,
                ];
            }

            $totauxParType[$type]['nombre']++;
            $totauxParType[$type]['total'] += $remuneration->getMontant();
        }

        arsort($totauxParType);

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

        $finder = new Finder();
        $finder->files()->in($this->getParameter('kernel.project_dir') . '/assets/images/avatars');

        $avatars = [];

        foreach ($finder as $file) {
            if ($file->getFilename() !== "avatar_default.png")
                $avatars[] = $file->getFilename();
        }

        sort($avatars);


        return $this->render('dashboard/enfant.html.twig', [
            'matieres' => $matieres,
            'moyenne' => $moyenneGenerale,
            'evaluationsParMatiere' => $evaluationsParMatiere ?? [],
            'enfant' => $enfant,
            'remunerations' => $remunerations,
            'remunerationsParType' => $remunerationsParType,
            'totauxParType' => $totauxParType,
            'avatars' => $avatars
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

    private function getScoreMoyen(array $scores)
    {
        $nbCompetences = count($scores);
        $total = 0;
        $moyenne = null;
        if (!empty($scores)) {
            foreach ($scores as $score) {
                $total += intval($score);
            }

            $moyenne = $total / $nbCompetences;
        }

        return $moyenne;
    }
}
