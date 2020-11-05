<?php

namespace App\Model\Slack;

use App\Handler\SlackHandler;
use App\Model\Github\PullRequest;

class ValidationRequired extends Validation
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
                "type" => "actions",
                "elements" => [
                    [
                        "type" => "button",
                        "text" => ["type" => "plain_text", "text" => "M'assigner la validation", "emoji" => false],
                        "action_id" => SlackHandler::ACTION_VALIDATION_ASSIGN,
                        "value" => json_encode([
                            'pull_request' => $this->pullRequest->normalize(),
                            'validation_env' => $this->reviewEnvironment,
                            'jira_issue_key' => $this->jiraIssueKey
                        ])
                    ]
                ]
            ]
        ];
    }
}
