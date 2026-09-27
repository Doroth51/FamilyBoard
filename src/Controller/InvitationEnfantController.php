<?php

namespace App\Controller;

use App\Entity\InvitationEnfant;
use App\Entity\Enfant;
use App\Form\InvitationEnfantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InvitationEnfantController extends AbstractController
{
    #[Route('/invite/enfant/{id}', name: 'invite_enfant')]
    public function invite(
        Enfant $enfant,
        Request $request,
        EntityManagerInterface $em,
        \Symfony\Component\Mailer\MailerInterface $mailer
    ): Response {

        $invitation = new InvitationEnfant();
        $invitation->setEnfant($enfant);

        $form = $this->createForm(InvitationEnfantType::class, $invitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($invitation);
            $em->flush();

            // Email
            $email = (new \Symfony\Component\Mime\Email())
                ->from('noreply@familyboard.fr')
                ->to($invitation->getEmail())
                ->subject('Invitation FamilyBoard')
                ->html("
                    <p>Bonjour,</p>
                    <p>Votre parent vous invite à rejoindre FamilyBoard.</p>
                    <p><a href='" . $this->generateUrl('enfant_register', [
                    'token' => $invitation->getToken()
                ], 0) . "'>Cliquez ici pour créer votre compte</a></p>
                ");

            $mailer->send($email);

            $this->addFlash('success', "Invitation envoyée !");
            return $this->redirectToRoute('dashboard_parent');
        }

        return $this->render('invite/enfant.html.twig', [
            'form' => $form->createView(),
            'enfant' => $enfant
        ]);
    }
}
