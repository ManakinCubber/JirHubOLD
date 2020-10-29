<?php

namespace App\Model\Slack;

use App\Helper\JiraHelper;
use App\Model\Github\PullRequest;

class ValidationRequired implements SlackMessage
{
    /** @var PullRequest */
    private $pullRequest;
    
    /** @var string */
    private $reviewEnvironment;
    
    /** @var string|null */
    private $jiraIssueKey;

    public function __construct(PullRequest $pullRequest, string $reviewEnvironment, ?string $jiraIssueKey)
    {
        $this->pullRequest       = $pullRequest;
        $this->reviewEnvironment = $reviewEnvironment;
        $this->jiraIssueKey      = $jiraIssueKey;
    }

    public function normalize(): array
    {
        $subject = $this->reviewEnvironment;

        if (null !== $this->jiraIssueKey) {
            $subject = JiraHelper::buildIssueUrlFromIssueName($this->jiraIssueKey);
        }

        return [
            'icon_emoji' => ':radioactive_sign:',
            'blocks'     => json_encode([
                [
                    "type" => "section",
                    "fields"=> [
                        [
                            "type" => "mrkdwn",
                            "text"=> "*Environement:* {$this->reviewEnvironment}"
                        ],
                        [
                            "type" => "mrkdwn",
                            "text"=> "*Auteur:* {$this->pullRequest->getUser()->getLogin()}"
                        ],
                        [
                            "type" => "mrkdwn",
                            "text"=> "*Issue:*\n{$subject}"
                        ],
                        [
                            "type" => "mrkdwn",
                            "text"=> "*Pull request:*\n{$this->pullRequest->getUrl()}"
                        ],
                    ]
                ],
                [
                    "type" => "actions",
                    "elements" => [
                        [
                            "type" => "button",
                            "text" => ["type" => "plain_text", "text" => "m'assigner la validation", "emoji" => false],
                            "action_id" => "assign-pull-request",
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
