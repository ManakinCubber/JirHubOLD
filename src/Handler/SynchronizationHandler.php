<?php

namespace App\Handler;

use App\Dashboard\Handler\DashboardHandler;
use App\Handler\SynchronizationCommand\AddValidationRequiredLabelCommand;
use App\Handler\SynchronizationCommand\DeleteValidationRequiredLabelCommand;
use App\Handler\SynchronizationCommand\SynchronizationCommandInterface;
use App\Handler\SynchronizationCommand\TransitionJiraIssueToInProgressCommand;
use App\Handler\SynchronizationCommand\TransitionJiraIssueToInReviewCommand;
use App\Handler\SynchronizationCommand\TransitionJiraIssueToToValidateCommand;
use App\Handler\SynchronizationCommand\UpdatePullRequestDescriptionCommand;
use App\Handler\SynchronizationCommand\UpdatePullRequestLabelsCommand;
use App\Handler\SynchronizationCommand\UpdatePullRequestTitleCommand;
use App\Model\JirHubTask;
use App\Repository\GitHub\PullRequestRepository;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

readonly class SynchronizationHandler
{
    public const RELEASE_PR_TITLE_PREFIX = 'MEP';

    public function __construct(
        private CacheItemPoolInterface $cache,
        private DeleteValidationRequiredLabelCommand $deleteValidationRequiredLabelCommand,
        private AddValidationRequiredLabelCommand $addValidationRequiredLabelCommand,
        private UpdatePullRequestLabelsCommand $updatePullRequestLabelsCommand,
        private UpdatePullRequestDescriptionCommand $updatePullRequestDescriptionCommand,
        private UpdatePullRequestTitleCommand $updatePullRequestTitleCommand,
        private TransitionJiraIssueToInProgressCommand $transitionJiraIssueToInProgressCommand,
        private TransitionJiraIssueToInReviewCommand $transitionJiraIssueToInReviewCommand,
        private TransitionJiraIssueToToValidateCommand $transitionJiraIssueToToValidateCommand
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function synchronize(JirHubTask $jirHubTask): void
    {
        $pullRequest = $jirHubTask->getGithubPullRequest();

        // TODO : Belongs in a pre-synchronization event listener
        $this->cache->deleteItem(DashboardHandler::CACHE_KEY);
        $this->cache->deleteItem(PullRequestRepository::DEFAULT_LIST);

        if (0 === mb_strpos($pullRequest->getTitle(), self::RELEASE_PR_TITLE_PREFIX)) {
            return;
        }

        $tasks = [
            $this->deleteValidationRequiredLabelCommand,
            $this->addValidationRequiredLabelCommand,
            $this->updatePullRequestLabelsCommand,
            $this->updatePullRequestDescriptionCommand,
            $this->updatePullRequestTitleCommand,
            $this->transitionJiraIssueToInProgressCommand,
            $this->transitionJiraIssueToInReviewCommand,
            $this->transitionJiraIssueToToValidateCommand,
        ];

        /** @var SynchronizationCommandInterface $task */
        foreach ($tasks as $task) {
            $task->execute($jirHubTask);
        }
    }
}
