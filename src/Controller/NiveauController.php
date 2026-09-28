<?php

namespace App\Controller;

use App\Entity\Niveau;
use App\Form\NiveauType;
use App\Repository\NiveauRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/niveau')]
class NiveauController extends AbstractController
{
    #[Route('/', name: 'niveau_index', methods: ['GET'])]
    public function index(NiveauRepository $niveauRepository): Response
    {
        return $this->render('niveau/index.html.twig', [
            'niveaux' => $niveauRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'niveau_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $niveau = new Niveau();
        $form = $this->createForm(NiveauType::class, $niveau);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($niveau);
            $em->flush();

            return $this->redirectToRoute('niveau_index');
        }

        return $this->render('niveau/new.html.twig', [
            'niveau' => $niveau,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'niveau_show', methods: ['GET'])]
    public function show(Niveau $niveau): Response
    {
        return $this->render('niveau/show.html.twig', [
            'niveau' => $niveau,
        ]);
    }

    #[Route('/{id}/edit', name: 'niveau_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Niveau $niveau, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(NiveauType::class, $niveau);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('niveau_index');
        }

        return $this->render('niveau/edit.html.twig', [
            'niveau' => $niveau,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'niveau_delete', methods: ['POST'])]
    public function delete(Request $request, Niveau $niveau, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $niveau->getId(), $request->request->get('_token'))) {
            $em->remove($niveau);
            $em->flush();
        }

        return $this->redirectToRoute('niveau_index');
    }
}
