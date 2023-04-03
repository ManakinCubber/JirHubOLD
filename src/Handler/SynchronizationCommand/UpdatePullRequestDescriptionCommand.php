<?php

namespace App\Handler\SynchronizationCommand;

use App\Model\JirHubTask;
use App\Repository\GitHub\Constant\PullRequestUpdatableFields;
use App\Repository\GitHub\PullRequestRepository;
use Psr\Log\LoggerInterface;

final readonly class UpdatePullRequestDescriptionCommand implements SynchronizationCommandInterface
{
    public function __construct(
        private PullRequestRepository $pullRequestRepository,
        private LoggerInterface $logger
    ) {
    }

    public function execute(JirHubTask $jirHubTask): void
    {
        $pullRequestBody = $jirHubTask->getGithubPullRequest()->getBody();
        $bodyPrefix = '> Cette _pull request_ a été ouverte sans ticket Jira associé 👎';

        if (null !== $jirHubTask->getJiraIssue()) {
            $bodyPrefix = $jirHubTask->getJiraIssue()->getUri()->__toString();
        }

        if (false === mb_strpos($pullRequestBody, $bodyPrefix)) {
            $this->pullRequestRepository->update(
                $jirHubTask->getGithubPullRequest(),
                [PullRequestUpdatableFields::BODY => $bodyPrefix."\n\n".$pullRequestBody]
            );

            $this->logger->info(
                sprintf('Updated pull request #%d description', $jirHubTask->getGithubPullRequest()->getId())
            );
        }
    }
}
