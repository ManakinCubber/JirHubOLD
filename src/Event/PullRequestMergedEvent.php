<?php

namespace App\Event;

use App\Model\Github\PullRequest;
use Symfony\Contracts\EventDispatcher\Event;

class PullRequestMergedEvent extends Event
{
    public function __construct(protected readonly PullRequest $pullRequest)
    {
    }

    public function getPullRequest(): PullRequest
    {
        return $this->pullRequest;
    }
}
