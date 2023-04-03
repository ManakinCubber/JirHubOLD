<?php

namespace App\Dashboard\Query\Adapter;

use App\Dashboard\Query\PullRequestsToDeploy;
use App\Repository\GitHub\Constant\PullRequestSearchFilters;
use App\Repository\GitHub\PullRequestRepository;

class FromRepositoryPullRequestsToDeploy implements PullRequestsToDeploy
{
    public function __construct(private readonly PullRequestRepository $pullRequestRepository)
    {
    }

    public function fetch(): array
    {
        return $this->pullRequestRepository->search(
            [PullRequestSearchFilters::LABELS => ['~validation-required']]
        );
    }
}
