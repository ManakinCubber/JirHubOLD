<?php

namespace App\Model;

use Psr\Http\Message\UriInterface;

class JiraIssue
{
    private \DateInterval $lifespan;

    public function __construct(
        private readonly int $id,
        private readonly string $key,
        private readonly string $summary,
        private readonly bool $flagged,
        private readonly string $priority,
        private readonly JiraIssueType $type,
        private readonly JiraIssueStatus $status,
        private readonly \DateTimeInterface $createdAt,
        private readonly UriInterface $uri,
        private readonly ?string $epicKey,
        private readonly ?\DateTimeInterface $publishedAt,
        private readonly ?\DateTimeInterface $resolvedAt
    ) {
        $this->computeLifespan();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function isFlagged(): bool
    {
        return $this->flagged;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function getType(): JiraIssueType
    {
        return $this->type;
    }

    public function getStatus(): JiraIssueStatus
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getLifespan(): \DateInterval
    {
        return $this->lifespan;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function getEpicKey(): ?string
    {
        return $this->epicKey;
    }

    public function getResolvedAt(): ?\DateTimeInterface
    {
        return $this->resolvedAt;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    private function computeLifespan(): void
    {
        $startDate = $this->publishedAt ?? $this->createdAt;
        $endDate = $this->resolvedAt ?? new \DateTimeImmutable();

        $this->lifespan = $startDate->diff($endDate);
    }
}
