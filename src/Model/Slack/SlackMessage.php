<?php

namespace App\Model\Slack;

interface SlackMessage
{
    public function normalize(): array;
}
