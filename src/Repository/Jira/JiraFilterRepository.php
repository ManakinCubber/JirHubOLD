<?php

namespace App\Repository\Jira;

use App\Client\JiraClient;
use App\Factory\JiraIssueFactory;
use App\Model\JiraFilter;

class JiraFilterRepository
{
    private const ROUTE_GET_FILTER = '/filter/%s';

    /** @var JiraClient */
    private $jiraClient;

    /** @var JiraIssueFactory */
    private $jiraIssueFactory;

    public function __construct(JiraClient $jiraClient, JiraIssueFactory $jiraIssueFactory)
    {
        $this->jiraClient       = $jiraClient;
        $this->jiraIssueFactory = $jiraIssueFactory;
    }

    public function find(int $id): JiraFilter
    {
        $filterData = $this->jiraClient->get(sprintf(self::ROUTE_GET_FILTER, $id));
        $issuesData = $this->jiraClient->get($filterData['searchUrl']);

        $issues = [];

        foreach ($issuesData['issues'] as $issueData) {
            $issues[] = $this->jiraIssueFactory->create($issueData);
        }

        return new JiraFilter($filterData['id'], $filterData['name'], $issues);
    }
}
