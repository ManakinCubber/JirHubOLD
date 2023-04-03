<?php

namespace App\Repository\GitHub;

use App\Client\GitHubClient;
use App\Factory\PullRequestReviewFactory;
use App\Model\Github\PullRequest;

readonly class PullRequestReviewRepository
{
    public function __construct(
        private GitHubClient $client,
        private string $repositoryOwner,
        private string $repositoryName,
        private PullRequestReviewFactory $pullRequestReviewFactory
    ) {
    }

    public function search(PullRequest $pullRequest, array $parameters = []): array
    {
        $reviews = [];
        $apiParameters = [
            'per_page' => 50,
        ];

        if (\array_key_exists('per_page', $parameters)) {
            $apiParameters['per_page'] = $parameters['per_page'];
        }

        $reviewsData = $this->client->reviews()->all(
            $this->repositoryOwner,
            $this->repositoryName,
            $pullRequest->getId(),
            $apiParameters
        );

        foreach ($reviewsData as $reviewData) {
            $reviews[] = $this->pullRequestReviewFactory->create($reviewData);
        }

        return $reviews;
    }
}
