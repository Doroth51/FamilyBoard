<?php

namespace App\Controller;

use App\Entity\RemunerationType;
use App\Form\RemunerationTypeType;
use App\Repository\RemunerationTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/remuneration-type')]
class RemunerationTypeController extends AbstractController
{
    #[Route('/', name: 'remuneration_type_index', methods: ['GET'])]
    public function index(RemunerationTypeRepository $repo): Response
    {
        return $this->render('remuneration_type/index.html.twig', [
            'types' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'remuneration_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $type = new RemunerationType();
        $form = $this->createForm(RemunerationTypeType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($type);
            $em->flush();
            return $this->redirectToRoute('remuneration_type_index');
        }

        return $this->render('remuneration_type/new.html.twig', [
            'type' => $type,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'remuneration_type_show', methods: ['GET'])]
    public function show(RemunerationType $type): Response
    {
        return $this->render('remuneration_type/show.html.twig', [
            'type' => $type,
        ]);
    }

    #[Route('/{id}/edit', name: 'remuneration_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RemunerationType $type, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RemunerationTypeType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('remuneration_type_index');
        }

        return $this->render('remuneration_type/edit.html.twig', [
            'type' => $type,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'remuneration_type_delete', methods: ['POST'])]
    public function delete(Request $request, RemunerationType $type, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $type->getId(), $request->request->get('_token'))) {
            $em->remove($type);
            $em->flush();
        }

        return $this->redirectToRoute('remuneration_type_index');
    }
}
