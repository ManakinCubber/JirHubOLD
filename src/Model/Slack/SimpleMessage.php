<?php

namespace App\Model\Slack;

readonly class SimpleMessage implements SlackMessage
{
    public function __construct(private string $message)
    {
    }

    public function normalize(): array
    {
        return ['text' => $this->message];
    }
}
