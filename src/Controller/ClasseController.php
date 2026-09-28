<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Form\ClasseType;
use App\Repository\ClasseRepository;
use App\Repository\NiveauRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/classe')]
class ClasseController extends AbstractController
{
    #[Route('/', name: 'classe_index', methods: ['GET'])]
    public function index(ClasseRepository $classeRepository, NiveauRepository $niveauRepository): Response
    {
        $niveaux = $classeRepository->findAll();

        return $this->render('classe/index.html.twig', [
            'classes' => $classeRepository->findAll(),
            'niveaux' => $niveaux
        ]);
    }

    #[Route('/new', name: 'classe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $classe = new Classe();
        $form = $this->createForm(ClasseType::class, $classe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($classe);
            $em->flush();

            return $this->redirectToRoute('classe_index');
        }

        return $this->render('classe/new.html.twig', [
            'classe' => $classe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'classe_show', methods: ['GET'])]
    public function show(Classe $classe): Response
    {
        return $this->render('classe/show.html.twig', [
            'classe' => $classe,
        ]);
    }

    #[Route('/{id}/edit', name: 'classe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Classe $classe, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ClasseType::class, $classe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('classe_index');
        }

        return $this->render('classe/edit.html.twig', [
            'classe' => $classe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'classe_delete', methods: ['POST'])]
    public function delete(Request $request, Classe $classe, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $classe->getId(), $request->request->get('_token'))) {
            $em->remove($classe);
            $em->flush();
        }

        return $this->redirectToRoute('classe_index');
    }
}
