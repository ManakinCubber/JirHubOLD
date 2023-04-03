<?php

namespace App\Repository\Jira;

use App\Client\JiraClient;
use App\Exception\UnexpectedContentType;
use App\Factory\JiraIssueFactory;
use App\Model\JiraIssue;
use App\Model\JiraTransition;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

readonly class JiraIssueRepository
{
    private const ROUTE_GET_ISSUE = '/issue/%s';
    private const ROUTE_POST_TRANSITION = '/issue/%s/transitions';
    private const ROUTE_SEARCH = '/search';

    public function __construct(private JiraClient $jiraClient, private JiraIssueFactory $jiraIssueFactory)
    {
    }

    /**
     * @throws UnexpectedContentType
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getIssue(string $issueKey): JiraIssue
    {
        $issueData = $this->jiraClient->get(
            sprintf(self::ROUTE_GET_ISSUE, $issueKey),
            ['expand' => 'transitions']
        );

        return $this->jiraIssueFactory->create($issueData);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws UnexpectedContentType
     */
    public function transitionIssueTo(string $issueKey, JiraTransition $jiraTransition): void
    {
        $this->jiraClient->post(
            sprintf(self::ROUTE_POST_TRANSITION, $issueKey),
            [],
            $jiraTransition->toArray()
        );
    }

    public function search(string $jql): array
    {
        $issues = [];
        $issuesData = $this->jiraClient->get(
            self::ROUTE_SEARCH,
            ['jql' => $jql]
        );

        foreach ($issuesData['issues'] as $issueData) {
            $issues[] = $this->jiraIssueFactory->create($issueData);
        }

        return $issues;
    }
}
