<?php

namespace App\Handler;

use App\Model\Github\PullRequest;
use App\Model\Slack\ValidationInProgress;

class SlackHandler
{
    public function handleInteraction(array $body): array
    {
        if ($body['actions'][0]['action_id'] === 'assign-pull-request') {
            return array_merge(
                ["replace_original" => true],
                (new ValidationInProgress(
                    PullRequest::denormalize($body['actions'][0]['value']['pull_request']),
                    $body['actions'][0]['value']['validation_env'],
                    $body['actions'][0]['value']['jira_issue_key'],
                    $body['user']['username']
                ))->normalize()
            );
        }

        return [];
    }
}
