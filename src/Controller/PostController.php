<?php

namespace App\Controller;

use App\Entity\Post;
use App\Service\FileManagerServiceInterface;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Security\UserAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $posts = $postRepository->findAll();
        $result = $paginator->paginate(
          $posts,
          $request->query->getInt('page', 1),
          $request->query->getInt('limit', 5)
        );

        return $this->render('post/index.html.twig', [
            'posts' => $result,
        ]);
    }

    #[Route('/new', name: 'post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FileManagerServiceInterface $fileManagerService): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $image = $form->get('image')->getData();
          if ($image){
            $fileName = $fileManagerService->imagePostUpload($image);
            $post->setImage($fileName);
          }
           $post->setUser($this->getUser());
           $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }

    #[Route('/{id}/edit', name: 'post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, FileManagerServiceInterface $fileManagerService): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        $image = $form->get('image')->getData();
        $imageOld = $post->getImage();
        if($image){
          if($imageOld){
            $fileManagerService->removePostImage($imageOld);
          }
          $fileName = $fileManagerService->imagePostUpload($image);
          $post->setImage($fileName);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, FileManagerServiceInterface $fileManagerService): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {

            $image = $post->getImage();
            if($image){
                $fileManagerService->removePostImage($image);
              }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
    }
}
