<?php

namespace App\Model;

use Psr\Http\Message\UriInterface;

class JiraIssue
{
    private int $id;
    private string $key;
    private string $summary;
    private bool $flagged;
    private string $priority;
    private JiraIssueType $type;
    private JiraIssueStatus $status;
    private \DateTimeInterface $createdAt;
    private UriInterface $uri;
    private \DateInterval $lifespan;
    private ?string $epicKey;
    private ?\DateTimeInterface $resolvedAt;
    private ?\DateTimeInterface $publishedAt;

    public function __construct(
        int $id,
        string $key,
        string $summary,
        bool $flagged,
        string $priority,
        JiraIssueType $type,
        JiraIssueStatus $status,
        \DateTimeInterface $createdAt,
        UriInterface $uri,
        ?string $epicKey,
        ?\DateTimeInterface $publishedAt,
        ?\DateTimeInterface $resolvedAt
    ) {
        $this->id          = $id;
        $this->key         = $key;
        $this->summary     = $summary;
        $this->flagged     = $flagged;
        $this->priority    = $priority;
        $this->type        = $type;
        $this->status      = $status;
        $this->createdAt   = $createdAt;
        $this->uri         = $uri;
        $this->epicKey     = $epicKey;
        $this->publishedAt = $publishedAt;
        $this->resolvedAt  = $resolvedAt;

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
        $endDate   = $this->resolvedAt  ?? new \DateTimeImmutable();

        $this->lifespan = $startDate->diff($endDate);
    }
}
