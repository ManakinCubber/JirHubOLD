<?php

namespace App\EventSubscriber;

use App\Event\PullRequestMergedEvent;
use App\Handler\GitHubHandler;
use App\Repository\GitHub\PullRequestLabelRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnPullRequestMergedGitHubSubscriber implements EventSubscriberInterface
{
    /** @var GitHubHandler */
    protected $gitHubHandler;

    /** @var PullRequestLabelRepository */
    protected $pullRequestLabelRepository;

    public function __construct(GitHubHandler $gitHubHandler, PullRequestLabelRepository $pullRequestLabelRepository)
    {
        $this->gitHubHandler              = $gitHubHandler;
        $this->pullRequestLabelRepository = $pullRequestLabelRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            PullRequestMergedEvent::class => 'updatePullRequest',
        ];
    }

    public function updatePullRequest(PullRequestMergedEvent $event)
    {
        try {
            $this->gitHubHandler->removeReviewLabels($event->getPullRequest());
            $this->pullRequestLabelRepository->create(
                $event->getPullRequest(),
                getenv('GITHUB_REVIEW_OK_LABEL')
            );
        } catch (\Throwable $t) {
        }
    }
}
