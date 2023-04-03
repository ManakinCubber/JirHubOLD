<?php

namespace App\Controller;

use App\Dashboard\Handler\DashboardHandler;
use App\Handler\ChangelogHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private readonly DashboardHandler $dashboardHandler,
        private readonly ChangelogHandler $changelogHandler
    ) {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index()
    {
        return $this->render(
            'dashboard/index.html.twig',
            array_merge(
                $this->dashboardHandler->getData(),
                $this->changelogHandler->getChangelogWithLinks('master', 'dev')
            )
        );
    }

    #[Route('/dashboard', name: 'get_dashboard', methods: ['GET'])]
    public function dashboard()
    {
        return $this->redirectToRoute('index', [], Response::HTTP_MOVED_PERMANENTLY);
    }
}
