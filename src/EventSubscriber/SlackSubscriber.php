<?php

namespace App\EventSubscriber;

use App\Event\LabelsAppliedEvent;
use App\Event\PullRequestMergeFailureEvent;
use App\Helper\JiraHelper;
use App\Model\Github\PullRequest;
use App\Model\Slack\SlackMessage;
use App\Model\Slack\ValidationRequired;
use JoliCode\Slack\Api\Client;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SlackSubscriber implements EventSubscriberInterface
{
    /** @var Client */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public static function getSubscribedEvents()
    {
        return [
            LabelsAppliedEvent::class           => 'onLabelsApplied',
            PullRequestMergeFailureEvent::class => 'onPrFail',
        ];
    }

    public function onPrFail(PullRequestMergeFailureEvent $event)
    {
        try {
            $this->sendRawMessage(
                sprintf(
                    "JirHub could not merge this pull request : %s \nError : %s",
                    $event->getPullRequest()->getUrl(),
                    $event->getMessage()
                ),
                getenv('SLACK_DEV_CHANNEL')
            );
        } catch (\Throwable $t) {
        }
    }

    public function onLabelsApplied(LabelsAppliedEvent $event)
    {
        try {
            $this->sendMessage(
                new ValidationRequired($event->getPullRequest(), $event->getReviewEnvironment(), $event->getJiraIssueKey()),
                getenv('SLACK_REVIEW_CHANNEL')
            );
        } catch (\Throwable $t) {
        }
    }

    protected function sendMessage(SlackMessage $message, string $channel = ''): void
    {
        if ('' === $channel) {
            $channel = getenv('SLACK_DEV_CHANNEL');
        }

        $this->client->chatPostMessage(array_merge(
            [
                'username' => 'JirHub',
                'channel'  => $channel
            ],
            $message->normalize()
        ));
    }

    protected function sendRawMessage(string $message, string $channel = '')
    {
        if ('' === $channel) {
            $channel = getenv('SLACK_DEV_CHANNEL');
        }
        $this->client->chatPostMessage([
            'username'   => 'JirHub',
            'text'       => $message,
            'icon_emoji' => ':eyes:',
            'channel'    => $channel,
        ]);
    }
}
