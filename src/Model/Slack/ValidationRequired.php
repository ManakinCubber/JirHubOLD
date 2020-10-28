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
            'icon_emoji' => ':radioactive_sign:',
            'blocks'     => json_encode([
                [
                    "type" => "section",
                    "text" => ["type" => "mrkdwn", "text" => $message]
                ],
                [
                    "type" => "actions",
                    "elements" => [
                        [
                            "type" => "button",
                            "text" => ["type" => "plain_text", "text" => "m'assigner la validation", "emoji" => false],
                            "action_id" => "assign-pull-request",
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
