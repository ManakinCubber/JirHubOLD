<?php

namespace App\Model\Slack;

use App\Helper\JiraHelper;
use App\Model\Github\PullRequest;

class ValidationRejected implements SlackMessage
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
                            "text"=> "Pull request: *{$this->pullRequest->getUrl()}*"
                        ],
                    ]
                ],
                [
                    "type" => "section",
                    "text" => ["type" => "mrkdwn", "text" => ":x: rejetée par @{$this->validator}"]
                ]
            ])
        ];
    }
}
