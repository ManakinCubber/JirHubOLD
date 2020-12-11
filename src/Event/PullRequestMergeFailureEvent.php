<?php

namespace App\Event;

use App\Model\Github\PullRequest;
use Symfony\Contracts\EventDispatcher\Event;

class PullRequestMergeFailureEvent extends Event
{
    /** @var PullRequest */
    protected $pullRequest;

    /** @var string */
    protected $message;

    public function __construct(PullRequest $pullRequest, string $message)
    {
        $this->pullRequest = $pullRequest;
        $this->message     = $message;
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
