<?php

namespace App\Model\Github;

use App\Constant\GithubLabels;
use App\Model\JiraIssue;

class PullRequest
{
    private ?JiraIssue $jiraIssue;
    private ?array $reviews;

    public function __construct(
        private readonly int $id,
        private readonly string $title,
        private readonly string $body,
        private readonly string $headRef,
        private readonly string $baseRef,
        private readonly string $url,
        private readonly string $headSha,
        private readonly GithubUser $user,
        private array $labels
    ) {
        $this->jiraIssue = null;
        $this->reviews = null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getHeadRef(): string
    {
        return $this->headRef;
    }

    public function getBaseRef(): string
    {
        return $this->baseRef;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getHeadSha(): string
    {
        return $this->headSha;
    }

    public function getUser(): GithubUser
    {
        return $this->user;
    }

    public function getLabels(): array
    {
        return $this->labels;
    }

    public function hasLabel(string $label): bool
    {
        return \in_array($label, $this->labels, true);
    }

    public function isInProgress(): bool
    {
        foreach (GithubLabels::getDevelopmentInProgressLabels() as $label) {
            if (true === \in_array($label, $this->labels, true)) {
                return true;
            }
        }

        return false;
    }

    public function addLabel(string $label): self
    {
        if (false === $this->hasLabel($label)) {
            $this->labels[] = $label;
        }

        return $this;
    }

    public function removeLabel(string $label): self
    {
        $index = array_search($label, $this->labels, true);

        if (false !== $index) {
            unset($this->labels[$index]);
        }

        return $this;
    }

    public function getJiraIssue(): ?JiraIssue
    {
        return $this->jiraIssue;
    }

    public function getReviews(): ?array
    {
        return $this->reviews;
    }

    public function setReviews(?array $reviews): self
    {
        $this->reviews = $reviews;

        return $this;
    }

    public function normalize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'headRef' => $this->headRef,
            'baseRef' => $this->baseRef,
            'url' => $this->url,
            'headSha' => $this->headSha,
            'user' => $this->user->normalize(),
            'labels' => $this->labels,
        ];
    }

    public static function denormalize(array $data): self
    {
        return new self(
            $data['id'],
            $data['title'],
            $data['body'],
            $data['headRef'],
            $data['baseRef'],
            $data['url'],
            $data['headSha'],
            GithubUser::denormalize($data['user']),
            $data['labels']
        );
    }
}
