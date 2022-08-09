<?php

namespace App\Dashboard\Query\Adapter;

use App\Dashboard\Query\ReviewEnvironments;
use App\Model\ReviewEnvironment;
use App\Repository\GitHub\Constant\PullRequestSearchFilters;
use App\Repository\GitHub\PullRequestRepository;
use App\Repository\JirHubTaskRepository;

class FromRepositoryReviewEnvironments implements ReviewEnvironments
{
    /** @var PullRequestRepository */
    private $pullRequestRepository;

    /** @var JirHubTaskRepository */
    private $jirHubTaskRepository;

    public function __construct(
        PullRequestRepository $pullRequestRepository,
        JirHubTaskRepository $jirHubTaskRepository
    ) {
        $this->pullRequestRepository = $pullRequestRepository;
        $this->jirHubTaskRepository  = $jirHubTaskRepository;
    }

    public function fetch(): array
    {
        $environments = [
            new ReviewEnvironment('blue', 'PL Invoice'),
            new ReviewEnvironment('green', 'PL Expert'),
            new ReviewEnvironment('yellow', 'PL Wallet'),
            new ReviewEnvironment('orange'),
            new ReviewEnvironment('red', 'PL Legal'),
            new ReviewEnvironment('pink', 'PL Accounts'),
        ];

        /** @var ReviewEnvironment $environment */
        foreach ($environments as $environment) {
            $pullRequestsOnEnvironment = $this->pullRequestRepository->search(
                [PullRequestSearchFilters::LABELS => ['~validation-' . $environment->getName()]]
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
