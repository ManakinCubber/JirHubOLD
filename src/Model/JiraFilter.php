<?php

namespace App\Model;

class JiraFilter
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var JiraIssue[] */
    private $issues;

    public function __construct(int $id, string $name, array $issues)
    {
        $this->id     = $id;
        $this->name   = $name;
        $this->issues = $issues;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIssues(): array
    {
        return $this->issues;
    }
}
