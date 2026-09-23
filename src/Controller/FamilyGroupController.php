<?php

namespace App\Controller;

use App\Entity\FamilyGroup;
use App\Form\FamilyGroupType;
use App\Repository\FamilyGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/family-group')]
class FamilyGroupController extends AbstractController
{
    #[Route('/', name: 'family_group_index', methods: ['GET'])]
    public function index(FamilyGroupRepository $repo): Response
    {
        return $this->render('family_group/index.html.twig', [
            'groups' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'family_group_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $group = new FamilyGroup();
        $form = $this->createForm(FamilyGroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($group);
            $em->flush();
            return $this->redirectToRoute('family_group_index');
        }

        return $this->render('family_group/new.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'family_group_show', methods: ['GET'])]
    public function show(FamilyGroup $group): Response
    {
        return $this->render('family_group/show.html.twig', [
            'group' => $group,
        ]);
    }

    #[Route('/{id}/edit', name: 'family_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FamilyGroup $group, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(FamilyGroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('family_group_index');
        }

        return $this->render('family_group/edit.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'family_group_delete', methods: ['POST'])]
    public function delete(Request $request, FamilyGroup $group, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $group->getId(), $request->request->get('_token'))) {
            $em->remove($group);
            $em->flush();
        }

        return $this->redirectToRoute('family_group_index');
    }
}
