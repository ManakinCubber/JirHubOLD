<?php

namespace App\Helper;

readonly class JiraHelper
{
    public function __construct(
        private string $jiraIssueRegexPattern,
        private string $jiraHost
    ) {
    }

    public function extractIssueKeyFromString(string $str): ?string
    {
        $matches = [];
        preg_match($this->jiraIssueRegexPattern, $str, $matches);

        if (1 === \count($matches)) {
            return $matches[0];
        }

        return null;
    }

    public function buildIssueUrlFromIssueName(string $issueName): string
    {
        return $this->jiraHost.'/browse/'.$issueName;
    }
}
