<?php

namespace App\Controller;

use App\Entity\Periode;
use App\Form\PeriodeType;
use App\Repository\PeriodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/periode')]
class PeriodeController extends AbstractController
{
    #[Route('/', name: 'periode_index', methods: ['GET'])]
    public function index(PeriodeRepository $repo): Response
    {
        return $this->render('periode/index.html.twig', [
            'periodes' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'periode_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $periode = new Periode();
        $form = $this->createForm(PeriodeType::class, $periode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($periode);
            $em->flush();
            return $this->redirectToRoute('periode_index');
        }

        return $this->render('periode/new.html.twig', [
            'periode' => $periode,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'periode_show', methods: ['GET'])]
    public function show(Periode $periode): Response
    {
        return $this->render('periode/show.html.twig', [
            'periode' => $periode,
        ]);
    }

    #[Route('/{id}/edit', name: 'periode_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Periode $periode, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PeriodeType::class, $periode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('periode_index');
        }

        return $this->render('periode/edit.html.twig', [
            'periode' => $periode,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'periode_delete', methods: ['POST'])]
    public function delete(Request $request, Periode $periode, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $periode->getId(), $request->request->get('_token'))) {
            $em->remove($periode);
            $em->flush();
        }

        return $this->redirectToRoute('periode_index');
    }
}
