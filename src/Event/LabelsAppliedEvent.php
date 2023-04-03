<?php

namespace App\Event;

use App\Model\Github\PullRequest;
use Symfony\Contracts\EventDispatcher\Event;

class LabelsAppliedEvent extends Event
{
    public function __construct(
        protected readonly PullRequest $pullRequest,
        protected readonly string $reviewEnvironment,
        protected readonly ?string $jiraIssueKey
    ) {
    }

    public function getPullRequest(): PullRequest
    {
        return $this->pullRequest;
    }

    public function getReviewEnvironment(): string
    {
        return $this->reviewEnvironment;
    }

    public function getJiraIssueKey(): ?string
    {
        return $this->jiraIssueKey;
    }
}
