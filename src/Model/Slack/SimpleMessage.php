<?php

namespace App\Model\Slack;

class SimpleMessage implements SlackMessage
{
    /** @var string */
    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function normalize(): array
    {
        return ['text' => $this->message];
    }
}
