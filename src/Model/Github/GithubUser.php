<?php

namespace App\Model\Github;

readonly class GithubUser
{
    public function __construct(
        private int $id,
        private string $login,
        private string $avatarUrl
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getAvatarUrl(): string
    {
        return $this->avatarUrl;
    }

    public function normalize(): array
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'avatarUrl' => $this->avatarUrl,
        ];
    }

    public static function denormalize(array $data): self
    {
        return new self(
            $data['id'],
            $data['login'],
            $data['avatarUrl']
        );
    }
}
