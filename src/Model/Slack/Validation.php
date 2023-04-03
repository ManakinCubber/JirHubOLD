<?php

namespace App\Model\Slack;

use App\Helper\JiraHelper;
use App\Model\Github\PullRequest;

abstract class Validation implements SlackMessage
{
    public function __construct(
        protected PullRequest $pullRequest,
        protected JiraHelper $jiraHelper,
        protected string $reviewEnvironment,
        protected ?string $jiraIssueKey
    ) {
    }

    public function normalize(): array
    {
        $subject = $this->reviewEnvironment;

        if (null !== $this->jiraIssueKey) {
            $subject = $this->jiraHelper->buildIssueUrlFromIssueName($this->jiraIssueKey);
        }

        return [
            'blocks' => json_encode(array_merge(
                [
                    [
                        'type' => 'section',
                        'fields' => [
                            [
                                'type' => 'mrkdwn',
                                'text' => "Environement: *{$this->reviewEnvironment}*",
                            ],
                            [
                                'type' => 'mrkdwn',
                                'text' => "Auteur: *{$this->pullRequest->getUser()->getLogin()}*",
                            ],
                            [
                                'type' => 'mrkdwn',
                                'text' => "Issue: *<{$subject}|{$this->jiraIssueKey}>*",
                            ],
                            [
                                'type' => 'mrkdwn',
                                'text' => "*<{$this->pullRequest->getUrl()}|Voir la Pull request>*",
                            ],
                        ],
                    ],
                ],
                $this->normalizeStep()
            )),
        ];
    }

    abstract public function normalizeStep(): array;
}
