<?php

namespace App\Controller;

use App\Entity\ResetPasswordToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{
    #[Route('/reset-password', name: 'reset_password_request')]
    public function request(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {

            $email = $request->request->get('email');
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user) {
                $token = new ResetPasswordToken($email);
                $em->persist($token);
                $em->flush();

                $resetUrl = $this->generateUrl('reset_password_new', [
                    'token' => $token->getToken()
                ], 0);

                $mail = (new Email())
                    ->from('noreply@familyboard.fr')
                    ->to($email)
                    ->subject('Réinitialisation du mot de passe')
                    ->html("
                        <p>Bonjour,</p>
                        <p>Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>
                        <p><a href='$resetUrl'>Réinitialiser mon mot de passe</a></p>
                    ");

                $mailer->send($mail);
            }

            return $this->redirectToRoute('reset_password_email');
        }

        return $this->render('security/reset_password_request.html.twig');
    }

    #[Route('/reset-password/email', name: 'reset_password_email')]
    public function emailSent(): Response
    {
        return $this->render('security/reset_password_email_sent.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'reset_password_new')]
    public function reset(
        string $token,
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {

        $tokenEntity = $em->getRepository(ResetPasswordToken::class)
            ->findOneBy(['token' => $token]);

        if (!$tokenEntity) {
            throw $this->createNotFoundException("Lien invalide.");
        }

        if ($request->isMethod('POST')) {

            $password = $request->request->get('password');

            $user = $em->getRepository(User::class)->findOneBy(['email' => $tokenEntity->getEmail()]);
            $user->setPassword($hasher->hashPassword($user, $password));

            $em->remove($tokenEntity);
            $em->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_new.html.twig', [
            'token' => $token
        ]);
    }
}
