<?php

namespace App\Handler\SynchronizationCommand;

use App\Constant\GithubLabels;
use App\Constant\JiraIssueTypes;
use App\Model\JirHubTask;
use App\Repository\GitHub\PullRequestLabelRepository;
use App\Repository\GitHub\PullRequestRepository;
use Psr\Log\LoggerInterface;

final class UpdatePullRequestLabelsCommand implements SynchronizationCommandInterface
{
    /** @var PullRequestRepository */
    private $pullRequestRepository;

    /** @var PullRequestLabelRepository */
    private $pullRequestLabelRepository;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        PullRequestRepository $pullRequestRepository,
        PullRequestLabelRepository $pullRequestLabelRepository,
        LoggerInterface $logger
    ) {
        $this->pullRequestRepository      = $pullRequestRepository;
        $this->pullRequestLabelRepository = $pullRequestLabelRepository;
        $this->logger                     = $logger;
    }

    public function execute(JirHubTask $jirHubTask): void
    {
        $pullRequest = $jirHubTask->getGithubPullRequest();
        $jiraIssue   = $jirHubTask->getJiraIssue();

        if (null === $jiraIssue) {
            return;
        }

        if (false === $pullRequest->hasLabel(GithubLabels::BUG)
            && JiraIssueTypes::BUG === $jiraIssue->getIssueType()->getName()) {
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
