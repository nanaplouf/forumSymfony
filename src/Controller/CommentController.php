<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/comment')]
final class CommentController extends AbstractController
{
    #[Route(name: 'app_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    #[Route('/new/{topicId}', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function new(int $topicId, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le topic correspondant
        $topic = $entityManager->getRepository(Topic::class)->find($topicId);

        if (!$topic) {
            throw $this->createNotFoundException("Topic non trouvé !");
        }
        // Créer le commentaire
        $comment = new Comment();
        $comment->setTopic($topic); // associer le topic ici

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Associe l'utilisateur au commentaire
            $comment->setUser($this->getUser());
            // Définit la date de création
            $comment->setCreationDate(new \DateTime());


            $entityManager->persist($comment);
            $entityManager->flush();

            // Redirige vers la page du Topic (app_topic_show)
            return $this->redirectToRoute('app_topic_show', [
                'id' => $topic->getId(),  // Passe l'ID du Topic à la route
            ], Response::HTTP_SEE_OTHER);
        };

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
    }
}
