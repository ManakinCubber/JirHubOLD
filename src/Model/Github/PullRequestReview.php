<?php

namespace App\Model\Github;

readonly class PullRequestReview
{
    public function __construct(
        private int $id,
        private GithubUser $user,
        private string $body,
        private string $state,
        private string $url
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): GithubUser
    {
        return $this->user;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
