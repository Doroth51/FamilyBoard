<?php

namespace App\Controller;

use App\Entity\Enfant;
use App\Form\EnfantType;
use App\Repository\EnfantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/enfant')]
class EnfantController extends AbstractController
{
    #[Route('/', name: 'enfant_index', methods: ['GET'])]
    public function index(EnfantRepository $enfantRepository): Response
    {
        return $this->render('enfant/index.html.twig', [
            'enfants' => $enfantRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'enfant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $enfant = new Enfant();
        $form = $this->createForm(EnfantType::class, $enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($enfant);
            $em->flush();

            return $this->redirectToRoute('enfant_index');
        }

        return $this->render('enfant/new.html.twig', [
            'enfant' => $enfant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'enfant_show', methods: ['GET'])]
    public function show(Enfant $enfant): Response
    {
        return $this->render('enfant/show.html.twig', [
            'enfant' => $enfant,
        ]);
    }

    #[Route('/{id}/edit', name: 'enfant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Enfant $enfant, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EnfantType::class, $enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('enfant_index');
        }

        return $this->render('enfant/edit.html.twig', [
            'enfant' => $enfant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'enfant_delete', methods: ['POST'])]
    public function delete(Request $request, Enfant $enfant, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $enfant->getId(), $request->request->get('_token'))) {
            $em->remove($enfant);
            $em->flush();
        }

        return $this->redirectToRoute('enfant_index');
    }
}
