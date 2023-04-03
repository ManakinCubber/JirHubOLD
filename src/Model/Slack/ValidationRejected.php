<?php

namespace App\Model\Slack;

use App\Model\Github\PullRequest;

class ValidationRejected extends Validation
{
    public function __construct(PullRequest $pullRequest, string $reviewEnvironment, ?string $jiraIssueKey, private readonly string $validator)
    {
        parent::__construct($pullRequest, $reviewEnvironment, $jiraIssueKey);
    }

    public function normalizeStep(): array
    {
        return [
            [
                'type' => 'section',
                'text' => ['type' => 'mrkdwn', 'text' => ":x: rejetée par @{$this->validator}"],
            ],
        ];
    }
}
