<?php

namespace App\Handler\SynchronizationCommand;

use App\Exception\UnexpectedContentType;
use App\Model\JiraTransition;
use App\Model\JirHubTask;
use App\Repository\Jira\JiraIssueRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final readonly class TransitionJiraIssueToInReviewCommand implements SynchronizationCommandInterface
{
    public function __construct(
        private JiraIssueRepository $jiraIssueRepository,
        private string $label,
        private int $statusId,
        private int $transitionId,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @throws UnexpectedContentType
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function execute(
        JirHubTask $jirHubTask
    ): void {
        if (null === $jiraIssue = $jirHubTask->getJiraIssue()) {
            return;
        }

        $pullRequest = $jirHubTask->getGithubPullRequest();

        if (
            false === $pullRequest->hasLabel($this->label)
            && $jiraIssue->getStatus()->getId() !== $this->statusId
        ) {
            $this->jiraIssueRepository->transitionIssueTo(
                $jiraIssue->getKey(),
                new JiraTransition(
                    $this->transitionId,
                    'JirHub transitioned this issue.'
                )
            );

            $this->logger->info(
                sprintf('Transitionned issue %s to In Review', $jiraIssue->getKey())
            );
        }
    }
}
