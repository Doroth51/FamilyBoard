<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\FamilyGroup;
use App\Entity\Invitation;
use App\Form\RegisterType;
use App\Repository\InvitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $hasher->hashPassword($user, $user->getPassword())
            );

            $user->setRoles(['ROLE_PARENT']);

            // Création du groupe familial
            $group = new FamilyGroup();
            $group->setName($user->getFamilyName());
            $group->addUser($user);

            $em->persist($user);
            $em->persist($group);
            $em->flush();

            $this->addFlash('success', 'Compte créé avec succès !');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/register/invite/{token}', name: 'register_from_invitation')]
    public function registerFromInvitation(
        string $token,
        InvitationRepository $repo,
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ) {
        $invitation = $repo->findOneBy(['token' => $token]);

        if (!$invitation || $invitation->getExpiresAt() < new \DateTimeImmutable()) {
            throw $this->createNotFoundException("Invitation invalide ou expirée.");
        }

        $user = new User();
        $user->setEmail($invitation->getEmail());
        $user->setRoles(['ROLE_PARENT']);

        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $form->get('password')->getData();
            $user->setPassword($hasher->hashPassword($user, $password));

            // Rattachement au FamilyGroup
            $invitation->getFamilyGroup()->addUser($user);

            $em->persist($user);
            $em->remove($invitation); // On supprime l’invitation
            $em->flush();

            $this->addFlash('success', 'Compte créé avec succès !');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('invitation/register_from_invitation.html.twig', [
            'form' => $form->createView(),
            'email' => $invitation->getEmail()
        ]);
    }
}
