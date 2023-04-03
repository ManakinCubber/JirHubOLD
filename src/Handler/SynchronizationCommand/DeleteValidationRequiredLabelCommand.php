<?php

namespace App\Handler\SynchronizationCommand;

use App\Handler\GitHubHandler;
use App\Model\JirHubTask;
use App\Repository\GitHub\PullRequestLabelRepository;
use Psr\Log\LoggerInterface;

final readonly class DeleteValidationRequiredLabelCommand implements SynchronizationCommandInterface
{
    public function __construct(
        private GitHubHandler $githubHandler,
        private string $label,
        private PullRequestLabelRepository $pullRequestLabelRepository,
        private LoggerInterface $logger
    ) {
    }

    public function execute(JirHubTask $jirHubTask): void
    {
        $pullRequest = $jirHubTask->getGithubPullRequest();

        if ($pullRequest->hasLabel($this->label)
            && (
                !$this->githubHandler->isPullRequestApproved($pullRequest)
                || $this->githubHandler->isDeployed($pullRequest)
                || $this->githubHandler->isValidated($pullRequest)
                || $pullRequest->isInProgress()
            )
        ) {
            $this->pullRequestLabelRepository->delete(
                $pullRequest,
                $this->label
            );

            $this->logger->info(
                sprintf(
                    'Removed label %s from pull request #%d',
                    $this->label,
                    $pullRequest->getId()
                )
            );
        }
    }
}
