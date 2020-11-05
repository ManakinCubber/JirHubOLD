<?php

namespace App\Model\Slack;

use App\Handler\SlackHandler;
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

        if (null !== $this->jiraIssueKey) {
            $subject = JiraHelper::buildIssueUrlFromIssueName($this->jiraIssueKey);
        }

        return [
            'blocks'     => json_encode([
                [
                    "type" => "section",
                    "fields"=> [
                        [
                            "type" => "mrkdwn",
                            "text"=> "Environement: *{$this->reviewEnvironment}*"
                        ],
                        [
                            "type" => "mrkdwn",
                            "text"=> "Auteur: *{$this->pullRequest->getUser()->getLogin()}*"
                        ],
                        [
                            "type" => "mrkdwn",
                            "text"=> "Issue: *<{$subject}|{$this->jiraIssueKey}>*"
                        ],
                        [
                            "type" => "mrkdwn",
                            "text"=> ": *<{$this->pullRequest->getUrl()}|Voir la Pull request>*"
                        ],
                    ]
                ],
                [
                    "type" => "section",
                    "text" => ["type" => "mrkdwn", "text" => ":male-detective: Validation en cours par @{$this->validator}"]
                ],
                [
                    "type" => "actions",
                    "elements" => [
                        [
                            "type" => "button",
                            "text" => ["type" => "plain_text", "text" => "Approuver", "emoji" => false],
                            "action_id" => SlackHandler::ACTION_VALIDATION_APPROVE,
                            "style" => "primary",
                            "value" => json_encode([
                                'pull_request' => $this->pullRequest->normalize(),
                                'validation_env' => $this->reviewEnvironment,
                                'jira_issue_key' => $this->jiraIssueKey
                            ])
                        ],
                        [
                            "type" => "button",
                            "text" => ["type" => "plain_text", "text" => "Rejeter", "emoji" => false],
                            "action_id" => SlackHandler::ACTION_VALIDATION_REJECT,
                            "style" => "danger",
                            "value" => json_encode([
                                'pull_request' => $this->pullRequest->normalize(),
                                'validation_env' => $this->reviewEnvironment,
                                'jira_issue_key' => $this->jiraIssueKey
                            ])
                        ]
                    ]
                ]
            ])
        ];
    }
}
