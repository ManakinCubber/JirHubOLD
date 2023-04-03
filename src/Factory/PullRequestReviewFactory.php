<?php

namespace App\Factory;

use App\Model\Github\PullRequestReview;

readonly class PullRequestReviewFactory
{
    public function __construct(private GithubUserFactory $githubUserFactory)
    {
    }

    public function create(array $pullRequestReviewData): PullRequestReview
    {
        return new PullRequestReview(
            $pullRequestReviewData['id'],
            $this->githubUserFactory->create($pullRequestReviewData['user']),
            $pullRequestReviewData['body'],
            $pullRequestReviewData['state'],
            $pullRequestReviewData['html_url']
        );
    }
}
