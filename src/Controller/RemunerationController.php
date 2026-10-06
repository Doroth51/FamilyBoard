<?php

namespace App\Controller;

use App\Entity\Enfant;
use App\Entity\Remuneration;
use App\Form\RemunerationFormType;
use App\Repository\RemunerationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/remuneration')]
class RemunerationController extends AbstractController
{
    #[Route('/', name: 'remuneration_index', methods: ['GET'])]
    public function index(RemunerationRepository $repo): Response
    {
        return $this->render('remuneration/index.html.twig', [
            'remunerations' => $repo->findAll(),
        ]);
    }

    #[Route('/new/{enfantId<\d+>}', name: 'remuneration_new_by_enfant', methods: ['GET', 'POST'])]
    public function newByEnfant(
        #[MapEntity(id: 'enfantId')] Enfant $enfant,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $rem = new Remuneration();
        $rem->setEnfant($enfant);

        $form = $this->createForm(RemunerationFormType::class, $rem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($rem);
            $em->flush();
            return $this->redirectToRoute('remuneration_index');
        }

        return $this->render('remuneration/enfant/new.html.twig', [
            'remuneration' => $rem,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'remuneration_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $rem = new Remuneration();

        $form = $this->createForm(RemunerationFormType::class, $rem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($rem);
            $em->flush();
            return $this->redirectToRoute('remuneration_index');
        }

        return $this->render('remuneration/new.html.twig', [
            'remuneration' => $rem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'remuneration_show', methods: ['GET'])]
    public function show(Remuneration $rem): Response
    {
        return $this->render('remuneration/show.html.twig', [
            'remuneration' => $rem,
        ]);
    }

    #[Route('/{id}/edit', name: 'remuneration_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Remuneration $rem, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RemunerationFormType::class, $rem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('remuneration_index');
        }

        return $this->render('remuneration/edit.html.twig', [
            'remuneration' => $rem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'remuneration_delete', methods: ['POST'])]
    public function delete(Request $request, Remuneration $rem, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $rem->getId(), $request->request->get('_token'))) {
            $em->remove($rem);
            $em->flush();
        }

        return $this->redirectToRoute('remuneration_index');
    }
}
