<?php

namespace App\Factory;

use App\Model\Github\PullRequest;

readonly class PullRequestFactory
{
    public function __construct(private GithubUserFactory $githubUserFactory)
    {
    }

    public function create(array $pullRequestData): PullRequest
    {
        return new PullRequest(
            $pullRequestData['number'],
            $pullRequestData['title'],
            $pullRequestData['body'] ?? '',
            $pullRequestData['head']['ref'],
            $pullRequestData['base']['ref'],
            $pullRequestData['html_url'],
            $pullRequestData['head']['sha'],
            $this->githubUserFactory->create($pullRequestData['user']),
            array_column($pullRequestData['labels'], 'name')
        );
    }
}
