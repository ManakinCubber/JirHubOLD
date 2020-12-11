<?php

namespace App\Model\Slack;

use App\Model\Github\PullRequest;

class ValidationApproved extends Validation
{
    /** @var string */
    private $validator;

    public function __construct(PullRequest $pullRequest, string $reviewEnvironment, ?string $jiraIssueKey, string $validator)
    {
        parent::__construct($pullRequest, $reviewEnvironment, $jiraIssueKey);
        $this->validator = $validator;
    }

    public function normalizeStep(): array
    {
        return [
            [
                'type' => 'section',
                'text' => ['type' => 'mrkdwn', 'text' => ":heavy_check_mark: Validé par @{$this->validator}"],
            ],
        ];
    }
}
