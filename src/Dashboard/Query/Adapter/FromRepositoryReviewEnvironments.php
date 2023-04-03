<?php

namespace App\Dashboard\Query\Adapter;

use App\Dashboard\Query\ReviewEnvironments;
use App\Model\ReviewEnvironment;
use App\Repository\GitHub\Constant\PullRequestSearchFilters;
use App\Repository\GitHub\PullRequestRepository;
use App\Repository\JirHubTaskRepository;

class FromRepositoryReviewEnvironments implements ReviewEnvironments
{
    public function __construct(
        private readonly PullRequestRepository $pullRequestRepository,
        private readonly JirHubTaskRepository $jirHubTaskRepository
    ) {
    }

    public function fetch(): array
    {
        $environments = [
            new ReviewEnvironment('blue', 'PL Invoice'),
            new ReviewEnvironment('black', 'PL Invoice'),
            new ReviewEnvironment('green', 'PL Expert'),
            new ReviewEnvironment('purple', 'PL Expert'),
            new ReviewEnvironment('grey', 'PL Expert'),
            new ReviewEnvironment('yellow', 'PL Wallet'),
            new ReviewEnvironment('white', 'PL Wallet'),
            new ReviewEnvironment('red', 'PL Legal'),
            new ReviewEnvironment('teal', 'PL Legal'),
            new ReviewEnvironment('pink', 'PL Accounts'),
            new ReviewEnvironment('brown', 'PL Accounts'),
            new ReviewEnvironment('orange', 'Transverse'),
        ];

        foreach ($environments as $environment) {
            $pullRequestsOnEnvironment = $this->pullRequestRepository->search(
                [PullRequestSearchFilters::LABELS => ['~validation-'.$environment->getName()]]
            );

            if (!empty($pullRequestsOnEnvironment)) {
                $jirHubTask = $this->jirHubTaskRepository->getJirHubTaskFromPullRequest(
                    array_pop($pullRequestsOnEnvironment)
                );

                $environment->setJirHubTask($jirHubTask);
            }
        }

        return $environments;
    }
}
