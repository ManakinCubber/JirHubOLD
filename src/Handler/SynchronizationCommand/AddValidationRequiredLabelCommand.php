<?php

namespace App\Handler\SynchronizationCommand;

use App\Handler\GitHubHandler;
use App\Model\JirHubTask;
use App\Repository\GitHub\PullRequestLabelRepository;
use Psr\Log\LoggerInterface;

final readonly class AddValidationRequiredLabelCommand implements SynchronizationCommandInterface
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

        if (
            false === $pullRequest->hasLabel($this->label)
            && $this->githubHandler->isPullRequestApproved($pullRequest)
            && false === $this->githubHandler->isDeployed($pullRequest)
            && false === $this->githubHandler->isValidated($pullRequest)
            && false === $pullRequest->isInProgress()
        ) {
            $this->pullRequestLabelRepository->create(
                $pullRequest,
                $this->label
            );

            $this->logger->info(
                sprintf(
                    'Added label %s to pull request #%d',
                    $this->label,
                    $pullRequest->getId()
                )
            );
        }
    }
}
