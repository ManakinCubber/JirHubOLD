<?php

namespace App\Repository\GitHub;

use App\Client\GitHubClient;

readonly class CommitRepository
{
    public function __construct(
        private GitHubClient $client,
        private string $repositoryOwner,
        private string $repositoryName
    ) {
    }

    public function getChangelog(string $base, string $head): array
    {
        return $this->client->commits()->compare(
            $this->repositoryOwner,
            $this->repositoryName,
            $base,
            $head
        );
    }
}
