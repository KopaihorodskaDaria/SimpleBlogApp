<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class UserWarningBannedEmail
{
    public function __construct(
        private MailerInterface $mailer
    ) {}

    public function sendWarningEmail(User $user): void
    {
        $email = (new TemplatedEmail())
            ->from('admin-blog-app@example.com')
            ->to($user->getEmail())
            ->subject('Warning from Blog App Admin')
            ->htmlTemplate('admin/warning_email.html.twig')
            ->context([
                'user' => $user,
            ]);

        $this->mailer->send($email);
    }

}
