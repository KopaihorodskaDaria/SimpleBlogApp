<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Component\Mime\Email;


use Twig\Environment;


class ValidateEmail
{
    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator,
        private Environment $twig,
        private EntityManagerInterface $entityManager,
    ) {}

    public function sendConfirmationEmail(User $user): void
    {
        // generate token
        $token = bin2hex(random_bytes(32));
        $user->setEmailVerificationToken($token);
        // save token in database
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // generate the confirmation URL
        $confirmationUrl = $this->urlGenerator->generate('app_verify_email', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

        // Letter email using twig template
        $email = (new Email())
            ->from('no-reply@blogapp.com')
            ->to($user->getEmail())
            ->subject('Email confirmation')
            ->html(
                $this->twig->render('registration/confirm_email.html.twig', [
                    'user' => $user,
                    'confirmationUrl' => $confirmationUrl,
                ])
            );

        // sent letter
        $this->mailer->send($email);
    }

}
