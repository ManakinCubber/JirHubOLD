<?php

namespace App\Event;

use App\Model\Github\PullRequest;
use Symfony\Contracts\EventDispatcher\Event;

class LabelsAppliedEvent extends Event
{
    /** @var PullRequest */
    protected $pullRequest;

    /** @var string */
    protected $reviewEnvironment;

    /** @var string|null */
    protected $jiraIssueKey;

    public function __construct(PullRequest $pullRequest, string $reviewEnvironment, ?string $jiraIssueKey)
    {
        $this->pullRequest       = $pullRequest;
        $this->reviewEnvironment = $reviewEnvironment;
        $this->jiraIssueKey      = $jiraIssueKey;
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
