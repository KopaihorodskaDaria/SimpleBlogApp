<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentTypeForm;
use App\Form\PostTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    #[Route('/post/{id}/comment', name: 'app_comment_new', methods: ['POST'])]
    public function new(Request $request, Post $post): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $comment = new Comment();
        $form = $this->createForm(CommentTypeForm::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->security->getUser());
            $comment->setPost($post);
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_post_index');
        }

        return $this->redirectToRoute('app_post_index');
    }

    #[Route('/comments/{id}', name: 'app_comment_list', methods: ['GET'])]
    public function list(Post $post): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentTypeForm::class, $comment);

        return $this->render('comment/index.html.twig', [
            'post' => $post,
            'comments' => $post->getComments(),
            'comment_form' => $form->createView(),
        ]);
    }

    #[Route('/comment/{id}/update', name: 'app_comment_update')]
    public function update(Comment $comment, Request $request): Response{
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($comment->getAuthor()->getId() !== $this->security->getUser()->getId()) {
            throw $this->createAccessDeniedException('You can not edit this comment');
        }


        $form = $this->createForm(CommentTypeForm::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_post_index');

        }
        return $this->render('comment/update.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),

        ]);
    }

    #[Route('/comment/{id}/delete', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Comment $comment, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $currentUser = $this->security->getUser();

        if ($comment->getAuthor()->getId() !== $currentUser->getId()&& !$this->isGranted('ROLE_ADMIN') ) {
            throw $this->createAccessDeniedException('You can not delete this comment');
        }

        if ($this->isCsrfTokenValid('delete_comment_' . $comment->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($comment);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index');
    }
}
