<?php

namespace App\Dashboard\Query\Adapter;

use App\Dashboard\Query\PullRequestsToMergeOnDev;
use App\Repository\GitHub\Constant\PullRequestSearchFilters;
use App\Repository\GitHub\PullRequestRepository;

class FromRepositoryPullRequestsToMergeOnDev implements PullRequestsToMergeOnDev
{
    /** @var PullRequestRepository */
    protected $pullRequestRepository;

    public function __construct(PullRequestRepository $pullRequestRepository)
    {
        $this->pullRequestRepository = $pullRequestRepository;
    }

    public function fetch(): array
    {
        return $this->pullRequestRepository->search(
            [PullRequestSearchFilters::LABELS => ['~validated']]
        );
    }
}
