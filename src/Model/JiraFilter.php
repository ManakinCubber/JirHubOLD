<?php

namespace App\Model;

readonly class JiraFilter
{
    public function __construct(
        private int $id,
        private string $name,
        private array $issues
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

    public function getIssues(): array
    {
        return $this->issues;
    }
}
