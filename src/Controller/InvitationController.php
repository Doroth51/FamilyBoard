<?php

namespace App\Controller;

use App\Entity\Invitation;
use App\Form\InvitationType;
use App\Repository\FamilyGroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class InvitationController extends AbstractController
{
    #[Route('/invite/{id}', name: 'invite_parent')]
    public function invite(
        Request $request,
        FamilyGroupRepository $groupRepo,
        UserRepository $userRepo,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        int $id
    ) {
        // Vérification du groupe
        $group = $groupRepo->find($id);
        if (!$group) {
            throw $this->createNotFoundException("Groupe familial introuvable.");
        }

        // Vérification que l'utilisateur appartient au groupe
        if (!$group->getUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException(
                "Vous ne pouvez inviter que des parents dans votre propre groupe familial."
            );
        }

        // Création de l'invitation
        $invitation = new Invitation();
        $invitation->setFamilyGroup($group);

        $form = $this->createForm(InvitationType::class, $invitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Vérifier si un compte existe déjà avec cet email
            if ($userRepo->findOneBy(['email' => $invitation->getEmail()])) {
                $this->addFlash('error', 'Un compte existe déjà avec cet email.');
                return $this->redirectToRoute('invite_parent', ['id' => $id]);
            }

            // Génération du token sécurisé
            $token = bin2hex(random_bytes(32));
            $invitation->setToken($token);

            $em->persist($invitation);
            $em->flush();

            // Génération du lien d'inscription
            $url = $this->generateUrl(
                'register_from_invitation',
                ['token' => $token],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            // Envoi de l'email
            $email = (new Email())
                ->from('noreply@familyboard.fr')
                ->to($invitation->getEmail())
                ->subject('Invitation à rejoindre FamilyBoard')
                ->html("
                    <h2>Bienvenue sur FamilyBoard</h2>
                    <p>Vous avez été invité à rejoindre un groupe familial.</p>
                    <p><a href='$url'>Cliquez ici pour créer votre compte</a></p>
                    <p>Ce lien expire dans 48 heures.</p>
                ");

            $mailer->send($email);

            $this->addFlash('success', 'Invitation envoyée avec succès !');
            return $this->redirectToRoute('dashboard_parent');
        }

        return $this->render('invitation/invite.html.twig', [
            'form' => $form->createView(),
            'group' => $group
        ]);
    }

    #[Route('/invitations/{id}', name: 'invitations_list')]
    public function listInvitations(
        int $id,
        FamilyGroupRepository $groupRepo
    ) {
        $group = $groupRepo->find($id);

        if (!$group) {
            throw $this->createNotFoundException("Groupe introuvable.");
        }

        if (!$group->getUsers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException("Accès refusé.");
        }

        return $this->render('invitation/list.html.twig', [
            'group' => $group,
            'invitations' => $group->getInvitations()
        ]);
    }
}
