<?php

namespace App\Model;

readonly class JiraIssueType
{
    public function __construct(
        private int $id,
        private string $name,
        private bool $subtask
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isSubtask(): bool
    {
        return $this->subtask;
    }
}
