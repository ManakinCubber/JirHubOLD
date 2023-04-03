<?php

namespace App\EventSubscriber;

use App\Event\PullRequestMergedEvent;
use App\Handler\GitHubHandler;
use App\Repository\GitHub\PullRequestLabelRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class OnPullRequestMergedGitHubSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected GitHubHandler $gitHubHandler,
        protected PullRequestLabelRepository $pullRequestLabelRepository
    ) {
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
