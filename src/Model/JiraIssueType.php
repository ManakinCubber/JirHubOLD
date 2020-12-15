<?php

namespace App\Model;

class JiraIssueType
{
    private int $id;
    private string $name;
    private bool $subtask;

    public function __construct(int $id, string $name, bool $subtask)
    {
        $this->id      = $id;
        $this->name    = $name;
        $this->subtask = $subtask;
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
