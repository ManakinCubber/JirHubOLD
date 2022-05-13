<?php

namespace App\Handler;

use App\Model\Github\PullRequest;
use App\Model\Slack\ValidationApproved;
use App\Model\Slack\ValidationInProgress;
use App\Model\Slack\ValidationRejected;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SlackHandler
{
    const ACTION_VALIDATION_ASSIGN  = 'assign-pull-request';
    const ACTION_VALIDATION_APPROVE = 'approve-pull-request';
    const ACTION_VALIDATION_REJECT  = 'reject-pull-request';

    /** @var HttpClientInterface */
    private $client;

    public function __construct(
        HttpClientInterface $client,
        string $token
    )
    {
        $this->client = $client->withOptions(
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json',
                ],
            ]
        );
    }

    public function handleInteraction(array $body): array
    {
        $value       = json_decode($body['actions'][0]['value'], true);
        $responseUrl = $body['response_url'];
        $actionId    = $body['actions'][0]['action_id'];

        switch ($actionId) {
            case self::ACTION_VALIDATION_ASSIGN:
                $this->client->post(
                    $responseUrl,
                    [
                        'json' => array_merge(
                            ['replace_original' => true],
                            (new ValidationInProgress(
                                PullRequest::denormalize($value['pull_request']),
                                $value['validation_env'],
                                $value['jira_issue_key'],
                                $body['user']['username']
                            ))->normalize()
                        ),
                    ]
                );

                break;
            case self::ACTION_VALIDATION_APPROVE:
                $this->client->post(
                    $responseUrl,
                    [
                        'json' => array_merge(
                            ['replace_original' => true],
                            (new ValidationApproved(
                                PullRequest::denormalize($value['pull_request']),
                                $value['validation_env'],
                                $value['jira_issue_key'],
                                $body['user']['username']
                            ))->normalize()
                        ),
                    ]
                );

                break;
            case self::ACTION_VALIDATION_REJECT:
                $this->client->post(
                    $responseUrl,
                    [
                        'json' => array_merge(
                            ['replace_original' => true],
                            (new ValidationRejected(
                                PullRequest::denormalize($value['pull_request']),
                                $value['validation_env'],
                                $value['jira_issue_key'],
                                $body['user']['username']
                            ))->normalize()
                        ),
                    ]
                );

                break;
        }

        return [];
    }
}
