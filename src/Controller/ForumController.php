<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ForumController extends AbstractController
{
    #[Route('/', name: 'app_forum')]
    public function index(): Response
    {
        return $this->render('forum/index.html.twig', []);
    }

    #[Route('/apropos', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('forum/about.html.twig', []);
    }
}
