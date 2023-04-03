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

final readonly class TransitionJiraIssueToInProgressCommand implements SynchronizationCommandInterface
{
    public function __construct(
        private JiraIssueRepository $jiraIssueRepository,
        private array $inProgressLabels,
        private int $globalTransitionId,
        private array $subTaskTransitions,
        private int $subTaskTypeId,
        private int $statusId,
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
    public function execute(JirHubTask $jirHubTask): void
    {
        if (null === $jiraIssue = $jirHubTask->getJiraIssue()) {
            return;
        }

        $pullRequest = $jirHubTask->getGithubPullRequest();

        foreach ($this->inProgressLabels as $inProgressLabel) {
            if (
                true === $pullRequest->hasLabel($inProgressLabel)
                && $jiraIssue->getStatus()->getId() !== $this->statusId
            ) {
                $transition = new JiraTransition($this->globalTransitionId, 'JirHub transitionned this issue');

                if ($jiraIssue->getType()->getId() === $this->subTaskTypeId) {
                    foreach ($this->subTaskTransitions as $subTaskTransition) {
                        if ($jiraIssue->getStatus()->getId() === $subTaskTransition['statusId']) {
                            $transition = new JiraTransition($subTaskTransition['transitionId'], 'JirHub transitionned this issue');

                            break;
                        }
                    }
                }

                $this->jiraIssueRepository->transitionIssueTo($jiraIssue->getKey(), $transition);
                $this->logger->info(
                    sprintf('Transitionned issue %s to In Progress', $jiraIssue->getKey())
                );

                return;
            }
        }
    }
}
