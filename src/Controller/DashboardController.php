<?php

namespace App\Controller;

use App\Dashboard\Handler\DashboardHandler;
use App\Handler\ChangelogHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /** @var DashboardHandler */
    protected $dashboardHandler;

    /** @var ChangelogHandler */
    protected $changelogHandler;

    public function __construct(DashboardHandler $dashboardHandler, ChangelogHandler $changelogHandler)
    {
        $this->dashboardHandler = $dashboardHandler;
        $this->changelogHandler = $changelogHandler;
    }

    /**
     * @Route("/dashboard", name="get_dashboard", methods={"GET"})
     */
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
}
