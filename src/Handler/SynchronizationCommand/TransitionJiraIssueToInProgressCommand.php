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

final class TransitionJiraIssueToInProgressCommand implements SynchronizationCommandInterface
{
    /** @var JiraIssueRepository */
    private $jiraIssueRepository;

    /** @var array */
    private $inProgressLabels;

    /** @var int */
    private $globalTransitionId;

    /** @var array */
    private $subTaskTransitions;

    /** @var int */
    private $subTaskTypeId;

    /** @var int */
    private $statusId;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        JiraIssueRepository $jiraIssueRepository,
        array $inProgressLabels,
        int $globalTransitionId,
        array $subTaskTransitions,
        int $subTaskTypeId,
        int $statusId,
        LoggerInterface $logger
    ) {
        $this->jiraIssueRepository = $jiraIssueRepository;
        $this->inProgressLabels    = $inProgressLabels;
        $this->globalTransitionId  = $globalTransitionId;
        $this->subTaskTransitions  = $subTaskTransitions;
        $this->subTaskTypeId       = $subTaskTypeId;
        $this->statusId            = $statusId;
        $this->logger              = $logger;
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

                if ($jiraIssue->getIssueType()->getId() === $this->subTaskTypeId) {
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
