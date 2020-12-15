<?php

namespace App\Factory;

use App\Model\JiraIssue;
use App\Model\JiraIssueStatus;
use App\Model\JiraIssueType;
use GuzzleHttp\Psr7\Uri;

class JiraIssueFactory
{
    private string $host;

    public function __construct(string $host)
    {
        $this->host = $host;
    }

    public function create(array $issueData): JiraIssue
    {
        return new JiraIssue(
            (int) $issueData['id'],
            $issueData['key'],
            $issueData['fields']['summary'],
            \is_array($issueData['fields']['customfield_10300']),
            $issueData['fields']['priority']['name'],
            new JiraIssueType(
                $issueData['fields']['issuetype']['id'],
                $issueData['fields']['issuetype']['name'],
                $issueData['fields']['issuetype']['subtask']
            ),
            new JiraIssueStatus(
                $issueData['fields']['status']['id'],
                $issueData['fields']['status']['name']
            ),
            new \DateTimeImmutable($issueData['fields']['created']),
            new Uri(
                sprintf(
                    '%s/browse/%s',
                    $this->host,
                    $issueData['key']
                )
            ),
            $issueData['fields']['customfield_10005'],
            null // $resolvedAt // TODO : get custom field
        );
    }
}
