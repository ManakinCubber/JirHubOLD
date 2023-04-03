<?php

namespace App\Handler\SynchronizationCommand;

use App\Constant\GithubLabels;
use App\Constant\JiraIssueTypes;
use App\Model\JirHubTask;
use App\Repository\GitHub\PullRequestLabelRepository;
use Psr\Log\LoggerInterface;

final readonly class UpdatePullRequestLabelsCommand implements SynchronizationCommandInterface
{
    public function __construct(
        private PullRequestLabelRepository $pullRequestLabelRepository,
        private LoggerInterface $logger
    ) {
    }

    public function execute(JirHubTask $jirHubTask): void
    {
        $pullRequest = $jirHubTask->getGithubPullRequest();
        $jiraIssue = $jirHubTask->getJiraIssue();

        if (null === $jiraIssue) {
            return;
        }

        if (false === $pullRequest->hasLabel(GithubLabels::BUG)
            && JiraIssueTypes::BUG === $jiraIssue->getType()->getName()) {
            $this->pullRequestLabelRepository->create(
                $pullRequest,
                GithubLabels::BUG
            );

            $this->logger->info(
                sprintf(
                    'Added label %s to pull request #%d',
                    GithubLabels::BUG,
                    $pullRequest->getId()
                )
            );
        }
    }
}
