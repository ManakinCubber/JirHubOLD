<?php

namespace App\Model;

use App\Model\Github\PullRequest;

readonly class JirHubTask
{
    public function __construct(
        private PullRequest $githubPullRequest,
        private ?JiraIssue $jiraIssue = null
    ) {
    }

    public function getGithubPullRequest(): PullRequest
    {
        return $this->githubPullRequest;
    }

    public function getJiraIssue(): ?JiraIssue
    {
        return $this->jiraIssue;
    }
}
