<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\UserWarningBannedEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    public function __construct(
        private PostRepository $postRepository,
        private UserRepository $userRepository,
        private UserWarningBannedEmail $mailer,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/posts', name: 'admin_all_posts')]
    public function allPosts(): Response
    {
        // only admin has access
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // get all posts ordered by creation date
        return $this->render('admin/all_posts.html.twig', [
            'posts' => $this->postRepository->findBy([], ['createdAt' => 'DESC']),
        ]);

    }

    #[Route('/users', name: 'admin_users_list')]
    public function listUsers(): Response
    {
        // only admin has access
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $this->userRepository->findAll();

        // show all users
        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/{id}/warn', name: 'admin_warn_user')]
    public function warnUser(User $user): Response
    {
        // only admin has access
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // send warning email to the user
        $this->mailer->sendWarningEmail($user);

        $this->addFlash('success', 'Warning email sent to user.');

        return $this->redirectToRoute('admin_users_list');
    }

    #[Route('/users/{id}/ban', name: 'admin_ban_user')]
    public function banUser(User $user): Response
    {
        // only admin has access
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // set user as BANNED
        $user->setIsBanned(true);
        $this->entityManager->flush();

        $this->addFlash('danger', 'User has been banned.');

        return $this->redirectToRoute('admin_users_list');
    }

}
