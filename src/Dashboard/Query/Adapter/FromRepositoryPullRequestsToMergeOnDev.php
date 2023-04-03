<?php

namespace App\Dashboard\Query\Adapter;

use App\Dashboard\Query\PullRequestsToMergeOnDev;
use App\Repository\GitHub\Constant\PullRequestSearchFilters;
use App\Repository\GitHub\PullRequestRepository;

class FromRepositoryPullRequestsToMergeOnDev implements PullRequestsToMergeOnDev
{
    public function __construct(private readonly PullRequestRepository $pullRequestRepository)
    {
    }

    public function fetch(): array
    {
        return $this->pullRequestRepository->search(
            [PullRequestSearchFilters::LABELS => ['~validated']]
        );
    }
}
