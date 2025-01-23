<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/posts', name: 'app_post_')]
class PostController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(PostRepository $postRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $posts = $postRepository->findBy([], ['id' => 'DESC']);
        $pagination = $paginator->paginate(
            $posts, /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            18 /* limit per page */
        );

        return $this->render('post/index.html.twig', [
            'posts' => $pagination,
            'title' => 'Posts',
            'description' => 'List of all the post ever written on this website',
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            $this->addFlash('error','You must be connected to create a post !');
            return $this->redirectToRoute('app_login');
        }

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setAuthor($user);
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{ref}', name: 'show', requirements: ['ref' => '[a-zA-Z0-9\-éèêëàâäîïôöùûüçñÑ&µ@]+'], methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{ref}/edit', name: 'edit', requirements: ['ref' => '[a-zA-Z0-9\-éèêëàâäîïôöùûüçñÑ&µ@]+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $userAdmin = $this->isGranted('ROLE_ADMIN');

        if (
            $post->getAuthor() === $user
            || $userAdmin
        ) {
            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($userAdmin) {
                    $post->setIsPublished(true);
                }
                $entityManager->persist($post);
                $entityManager->flush();
                $this->addFlash('success', 'Your post has been successfully edited !');
                return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('post/edit.html.twig', [
                'post' => $post,
                'form' => $form,
            ]);
        } else {
            $this->addFlash('warning', 'You are not allowed to edit this element !!!');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/{ref}', name: 'delete', requirements: ['ref' => '[a-zA-Z0-9\-éèêëàâäîïôöùûüçñÑ&µ@]+'], methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($post->getAuthor() === $user || $this->isGranted('ROLE_ADMIN')) {
            if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
                $entityManager->remove($post);
                $entityManager->flush();
                $this->addFlash('success', 'Your post has been successfully deleted !');
            }
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
