<?php

namespace App\Event;

use App\Model\Github\PullRequest;
use Symfony\Contracts\EventDispatcher\Event;

class PullRequestMergedEvent extends Event
{
    /** @var PullRequest */
    protected $pullRequest;

    public function __construct(PullRequest $pullRequest)
    {
        $this->pullRequest = $pullRequest;
    }

    public function getPullRequest(): PullRequest
    {
        return $this->pullRequest;
    }
}
