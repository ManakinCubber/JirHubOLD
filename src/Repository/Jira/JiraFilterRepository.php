<?php

namespace App\Repository\Jira;

use App\Client\JiraClient;
use App\Model\JiraFilter;

class JiraFilterRepository
{
    private const ROUTE_GET_FILTER = '/filter/%s';

    /** @var JiraClient */
    private $jiraClient;

    /** @var JiraIssueRepository */
    private $jiraIssueRepository;

    public function __construct(JiraClient $jiraClient, JiraIssueRepository $jiraIssueRepository)
    {
        $this->jiraClient          = $jiraClient;
        $this->jiraIssueRepository = $jiraIssueRepository;
    }

    public function find(int $id): JiraFilter
    {
        $filterData = $this->jiraClient->get(sprintf(self::ROUTE_GET_FILTER, $id));
        $issues     = $this->jiraIssueRepository->search($filterData['jql']);

        return new JiraFilter($filterData['id'], $filterData['name'], $issues);
    }
}
