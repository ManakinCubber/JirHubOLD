<?php

namespace App\Model\Slack;

use App\Helper\JiraHelper;
use App\Model\Github\PullRequest;

class ValidationInProgress implements SlackMessage
{
    /** @var PullRequest */
    private $pullRequest;
    
    /** @var string */
    private $reviewEnvironment;
    
    /** @var string|null */
    private $jiraIssueKey;
    
    /** @var string */
    private $validator;

    public function __construct(PullRequest $pullRequest, string $reviewEnvironment, ?string $jiraIssueKey, string $validator)
    {
        $this->pullRequest       = $pullRequest;
        $this->reviewEnvironment = $reviewEnvironment;
        $this->jiraIssueKey      = $jiraIssueKey;
        $this->validator         = $validator;
    }

    public function normalize(): array
    {

        $subject = $this->reviewEnvironment;
        $blame   = '(demander à ' . $this->pullRequest->getUser()->getLogin() . ' de retrouver la tâche Jira)';

        if (null !== $this->jiraIssueKey) {
            $subject = JiraHelper::buildIssueUrlFromIssueName($this->jiraIssueKey);
            $blame   = '';
        }

        $message = sprintf(
            "%s dispo sur `%s` %s\n Pull Request : %s",
            $subject,
            $this->reviewEnvironment,
            $blame,
            $this->pullRequest->getUrl()
        );

        return [
            'icon_emoji' => ':male-detective:',
            'blocks'     => json_encode([
                [
                    "type" => "section",
                    "text" => ["type" => "mrkdwn", "text" => $message]
                ],
                [
                    "type" => "section",
                    "text" => ["type" => "mrkdwn", "text" => "Validation en cours par @{$this->validator}"]
                ],
                [
                    "type" => "actions",
                    "elements" => [
                        [
                            "type" => "button",
                            "text" => ["type" => "plain_text", "text" => "approuver", "emoji" => false],
                            "action_id" => "approve-pull-request",
                            "value" => [
                                'pull_request' => $this->pullRequest->normalize(),
                                'validation_env' => $this->reviewEnvironment,
                                'jira_issue_key' => $this->jiraIssueKey
                            ]
                        ]
                    ]
                ],
                [
                    "type" => "actions",
                    "elements" => [
                        [
                            "type" => "button",
                            "text" => ["type" => "plain_text", "text" => "rejeter", "emoji" => false],
                            "action_id" => "reject-pull-request",
                            "value" => [
                                'pull_request' => $this->pullRequest->normalize(),
                                'validation_env' => $this->reviewEnvironment,
                                'jira_issue_key' => $this->jiraIssueKey
                            ]
                        ]
                    ]
                ]
            ])
        ];
    }
}
