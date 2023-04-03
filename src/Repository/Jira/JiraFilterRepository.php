<?php

namespace App\Repository\Jira;

use App\Client\JiraClient;
use App\Model\JiraFilter;

readonly class JiraFilterRepository
{
    private const ROUTE_GET_FILTER = '/filter/%s';

    public function __construct(private JiraClient $jiraClient, private JiraIssueRepository $jiraIssueRepository)
    {
    }

    public function find(int $id): JiraFilter
    {
        $filterData = $this->jiraClient->get(sprintf(self::ROUTE_GET_FILTER, $id));
        $issues = $this->jiraIssueRepository->search($filterData['jql']);

        return new JiraFilter($filterData['id'], $filterData['name'], $issues);
    }
}
