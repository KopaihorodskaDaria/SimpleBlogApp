<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationForm;
use Psr\Log\LoggerInterface;

use App\Service\ValidateEmail;
use Random\RandomException;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Command\UserPasswordHashCommand;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class RegistrationController extends AbstractController
{
    public function __construct(
        private ValidateEmail $validateEmail,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/register', name: 'app_register')]
    public function register( Request $request, UserPasswordHasherInterface $passwordHasher,  ValidateEmail $validateEmail): Response
    {
        // create a new user
        $user = new User();
        // create and handle the registration form
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // hash the plain password from the form
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));


            // set creation date and verification status
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setIsVerified(false);


            // save the user to the database
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // send confirm email with verification token
            $this->validateEmail->sendConfirmationEmail($user);
            $this->addFlash('success', 'Registration successful! Please check your email to confirm.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
