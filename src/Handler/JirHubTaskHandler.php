<?php

namespace App\Handler;

use App\Exception\PullRequestNotFoundException;
use App\Exception\UnexpectedContentType;
use App\Helper\JiraHelper;
use App\Model\JirHubTask;
use App\Repository\Jira\JiraIssueRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

readonly class JirHubTaskHandler
{
    public function __construct(
        private GitHubHandler $githubHandler,
        private JiraIssueRepository $jiraIssueRepository,
        private JiraHelper $jiraHelper
    ) {
    }

    /**
     * @throws UnexpectedContentType
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws InvalidArgumentException
     * @throws PullRequestNotFoundException
     */
    public function getJirHubTaskFromGithubWebhookData(array $webhookData): JirHubTask
    {
        $jiraIssue = null;
        $pullRequest = $this->githubHandler->getPullRequestFromWebhookData($webhookData);
        $issueKey = $this->jiraHelper->extractIssueKeyFromString($pullRequest->getHeadRef())
            ?? $this->jiraHelper->extractIssueKeyFromString($pullRequest->getTitle());

        if (null !== $issueKey) {
            $jiraIssue = $this->jiraIssueRepository->getIssue($issueKey);
        }

        return new JirHubTask($pullRequest, $jiraIssue);
    }
}
