<?php

namespace App\Controller;

use App\Entity\Enfant;
use App\Entity\Evaluation;
use App\Form\EvaluationType;
use App\Repository\EvaluationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/evaluation')]
class EvaluationController extends AbstractController
{
    #[Route('/', name: 'evaluation_index', methods: ['GET'])]
    public function index(EvaluationRepository $evaluationRepository): Response
    {
        return $this->render('evaluation/index.html.twig', [
            'evaluations' => $evaluationRepository->findAll(),
        ]);
    }

    #[Route('/new/{enfantId<\d+>}', name: 'evaluation_new', methods: ['GET', 'POST'])]
    public function new(
        #[MapEntity(id: 'enfantId')] Enfant $enfant,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $evaluation = new Evaluation();
        $evaluation->setEnfant($enfant);

        $form = $this->createForm(EvaluationType::class, $evaluation, [
            'enfant' => $enfant
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($evaluation);
            $em->flush();

            return $this->redirectToRoute('dashboard_enfant', ["id" => $enfant->getId()]);
        }

        return $this->render('evaluation/new.html.twig', [
            'evaluation' => $evaluation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'evaluation_show', methods: ['GET'])]
    public function show(Evaluation $evaluation): Response
    {
        return $this->render('evaluation/show.html.twig', [
            'evaluation' => $evaluation,
        ]);
    }

    // #[Route('/{id}/edit', name: 'evaluation_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Evaluation $evaluation, EntityManagerInterface $em): Response
    // {
    //     $form = $this->createForm(EvaluationType::class, $evaluation);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $em->flush();

    //         return $this->redirectToRoute('evaluation_index');
    //     }

    //     return $this->render('evaluation/edit.html.twig', [
    //         'evaluation' => $evaluation,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}', name: 'evaluation_delete', methods: ['POST'])]
    public function delete(Request $request, Evaluation $evaluation, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid(
            'delete' . $evaluation->getId(),
            $request->request->get('_token')
        )) {

            $remuneration = $evaluation->getRemuneration();
            if ($remuneration) {
                $em->remove($remuneration);
            }
            $em->remove($evaluation);
            $em->flush();

            $this->addFlash('success', 'L\'évaluation a été supprimée.');
        }

        return $this->redirectToRoute('enfant_show', [
            'id' => $evaluation->getEnfant()->getId()
        ]);
    }
}
