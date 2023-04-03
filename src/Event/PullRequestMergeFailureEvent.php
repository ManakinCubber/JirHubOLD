<?php

namespace App\Event;

use App\Model\Github\PullRequest;
use Symfony\Contracts\EventDispatcher\Event;

class PullRequestMergeFailureEvent extends Event
{
    public function __construct(
        protected readonly PullRequest $pullRequest,
        protected readonly string $message
    ) {
    }

    public function getPullRequest(): PullRequest
    {
        return $this->pullRequest;
    }

    public function setPullRequest(PullRequest $pullRequest): self
    {
        $this->pullRequest = $pullRequest;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
