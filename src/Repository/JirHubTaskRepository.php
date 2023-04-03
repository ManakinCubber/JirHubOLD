<?php

namespace App\Repository;

use App\Helper\JiraHelper;
use App\Model\Github\PullRequest;
use App\Model\JirHubTask;
use App\Repository\Jira\JiraIssueRepository;

readonly class JirHubTaskRepository
{
    public function __construct(
        private JiraIssueRepository $jiraIssueRepository,
        private JiraHelper $jiraHelper
    ) {
    }

    public function getJirHubTaskFromPullRequest(PullRequest $pullRequest): JirHubTask
    {
        $jiraIssue = null;
        $key = $this->jiraHelper->extractIssueKeyFromString($pullRequest->getHeadRef())
            ?? $this->jiraHelper->extractIssueKeyFromString($pullRequest->getTitle());

        if (null !== $key) {
            try {
                $jiraIssue = $this->jiraIssueRepository->getIssue($key);
            } catch (\Throwable $t) {
            }
        }

        return new JirHubTask($pullRequest, $jiraIssue);
    }
}
