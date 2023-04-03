<?php

namespace App\Repository\GitHub;

use App\Client\GitHubClient;
use App\Model\Github\PullRequest;

readonly class PullRequestLabelRepository
{
    public function __construct(
        private GitHubClient $client,
        private string $repositoryOwner,
        private string $repositoryName
    ) {
    }

    public function create(PullRequest $pullRequest, string $label): void
    {
        $labels = $pullRequest->getLabels();

        if (\in_array($label, $labels, true)) {
            return;
        }

        $this->client->labels()->add(
            $this->repositoryOwner,
            $this->repositoryName,
            $pullRequest->getId(),
            $label
        );

        $pullRequest->addLabel($label);
    }

    public function delete(PullRequest $pullRequest, string $label): void
    {
        $labels = $pullRequest->getLabels();

        if (!\in_array($label, $labels, true)) {
            return;
        }

        $this->client->labels()->remove(
            $this->repositoryOwner,
            $this->repositoryName,
            $pullRequest->getId(),
            $label
        );

        $pullRequest->removeLabel($label);
    }
}
