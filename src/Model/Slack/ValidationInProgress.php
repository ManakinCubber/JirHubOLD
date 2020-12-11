<?php

namespace App\Model\Slack;

use App\Handler\SlackHandler;
use App\Model\Github\PullRequest;

class ValidationInProgress extends Validation
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
                'text' => ['type' => 'mrkdwn', 'text' => ":male-detective: Validation en cours par @{$this->validator}"],
            ],
            [
                'type'     => 'actions',
                'elements' => [
                    [
                        'type'      => 'button',
                        'text'      => ['type' => 'plain_text', 'text' => 'Approuver', 'emoji' => false],
                        'action_id' => SlackHandler::ACTION_VALIDATION_APPROVE,
                        'style'     => 'primary',
                        'value'     => json_encode([
                            'pull_request'   => $this->pullRequest->normalize(),
                            'validation_env' => $this->reviewEnvironment,
                            'jira_issue_key' => $this->jiraIssueKey,
                        ]),
                    ],
                    [
                        'type'      => 'button',
                        'text'      => ['type' => 'plain_text', 'text' => 'Rejeter', 'emoji' => false],
                        'action_id' => SlackHandler::ACTION_VALIDATION_REJECT,
                        'style'     => 'danger',
                        'value'     => json_encode([
                            'pull_request'   => $this->pullRequest->normalize(),
                            'validation_env' => $this->reviewEnvironment,
                            'jira_issue_key' => $this->jiraIssueKey,
                        ]),
                    ],
                ],
            ],
        ];
    }
}
