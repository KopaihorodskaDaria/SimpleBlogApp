<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostTypeForm;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class PostController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
        private SluggerInterface $slugger,
    ) {}

    #[Route('/', name: 'app_post_index')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy([], ['id' => 'DESC']);

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/post/new', name: 'app_post_new')]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $post = new Post();
        $post->setAuthor($this->security->getUser());
        $post->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(PostTypeForm::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                try {
                    $newFilename = $this->uploadImage($imageFile);
                    $post->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed uploading image.');

                }
            }
            $this->entityManager->persist($post);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_post_index');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/post/{id}/delete', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Post $post, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($post->getAuthor() !== $this->security->getUser()&& !$this->isGranted('ROLE_ADMIN') ) {
            throw $this->createAccessDeniedException('You can only delete your own posts');
        }

        if ($this->isCsrfTokenValid('delete_post_' . $post->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($post);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index');
    }

    #[Route('/post/{id}/update', name: 'app_post_update')]
    public function update(Post $post, Request $request): Response{
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($post->getAuthor() !== $this->security->getUser()) {
            throw $this->createAccessDeniedException('You can not edit this post');

        }

        $form = $this->createForm(PostTypeForm::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                try {
                    $newFilename = $this->uploadImage($imageFile);
                    $post->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed uploading image.');
                }
            }
            $this->entityManager->flush();
            return $this->redirectToRoute('app_post_index');

        }
        return $this->render('post/update.html.twig', [
            'post' => $post,
            'form' => $form->createView(),

        ]);
    }

    private function uploadImage($imageFile): string
    {
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

        $imageFile->move(
            $this->getParameter('uploads_directory'),
            $newFilename
        );

        return $newFilename;

    }

}

