<?php

namespace App\Controller;

use App\Handler\ChangelogHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChangelogController extends AbstractController
{
    public function __construct(
        private readonly ChangelogHandler $handler
    ) {
    }

    #[Route('/changelog', name: 'get_changelog', methods: ['GET'])]
    public function index(): Response
    {
        $response = new Response(implode(PHP_EOL, $this->handler->getChangelog('master', 'dev')));
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }

    #[Route('/commits', name: 'commits', methods: ['GET'])]
    public function commits(): Response
    {
        return $this->render('dashboard/commits.html.twig', $this->handler->getChangelogWithLinks('master', 'dev'));
    }
}
