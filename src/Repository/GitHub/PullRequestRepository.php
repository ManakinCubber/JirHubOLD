<?php

namespace App\Repository\GitHub;

use App\Event\PullRequestMergedEvent;
use App\Event\PullRequestMergeFailureEvent;
use App\Model\PullRequest;
use Github\Client;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PullRequestRepository
{
    /** @var Client */
    private $client;

    /** @var string */
    private $repositoryOwner;

    /** @var string */
    private $repositoryName;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        Client $client,
        string $repositoryOwner,
        string $repositoryName,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->client          = $client;
        $this->repositoryOwner = $repositoryOwner;
        $this->repositoryName  = $repositoryName;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function fetch($id): PullRequest
    {
        $pullRequestData = $this->client->pullRequests()->show(
            $this->repositoryOwner,
            $this->repositoryName,
            $id
        );

        return new PullRequest($pullRequestData);
    }

    /**
     * @return PullRequest[]
     */
    public function search(array $parameters = []): array
    {
        $apiParameters = [
            PullRequestSearchFilters::STATE            => 'open',
            PullRequestSearchFilters::RESULTS_PER_PAGE => 50,
        ];

        if (\array_key_exists(PullRequestSearchFilters::RESULTS_PER_PAGE, $parameters)) {
            $apiParameters[PullRequestSearchFilters::RESULTS_PER_PAGE] = $parameters[PullRequestSearchFilters::RESULTS_PER_PAGE];
            unset($parameters[PullRequestSearchFilters::RESULTS_PER_PAGE]);
        }

        if (\array_key_exists(PullRequestSearchFilters::STATE, $parameters)) {
            $apiParameters[PullRequestSearchFilters::STATE] = $parameters[PullRequestSearchFilters::STATE];
            unset($parameters[PullRequestSearchFilters::STATE]);
        }

        $pullRequestsData = $this->client->pullRequests()->all(
            $this->repositoryOwner,
            $this->repositoryName,
            $apiParameters
        );

        $pullRequests = [];

        foreach ($pullRequestsData as $pullRequestData) {
            $pullRequests[] = new PullRequest($pullRequestData);
        }

        foreach ($pullRequests as $key => $pullRequest) {
            if (
                \array_key_exists(PullRequestSearchFilters::TITLE, $parameters)
                && false === strpos($pullRequest->getTitle(), $parameters[PullRequestSearchFilters::TITLE])
            ) {
                unset($pullRequests[$key]);

                continue;
            }

            if (
                \array_key_exists(PullRequestSearchFilters::LABELS, $parameters)
                && false === empty(array_diff($parameters[PullRequestSearchFilters::LABELS], $pullRequest->getLabels()))
            ) {
                unset($pullRequests[$key]);

                continue;
            }

            if (
                \array_key_exists(PullRequestSearchFilters::HEAD_REF, $parameters)
                && false === strpos($pullRequest->getHeadRef(), $parameters[PullRequestSearchFilters::HEAD_REF])
            ) {
                unset($pullRequests[$key]);

                continue;
            }

            if (
                \array_key_exists(PullRequestSearchFilters::BASE_REF, $parameters)
                && false === strpos($pullRequest->getBaseRef(), $parameters[PullRequestSearchFilters::BASE_REF])
            ) {
                unset($pullRequests[$key]);

                continue;
            }
        }

        return $pullRequests;
    }

    public function update(PullRequest $pullRequest): void
    {
        return;
    }

    public function merge(PullRequest $pullRequest, $mergeMethod = 'squash'): void
    {
        try {
            $this->client->pullRequests()->merge(
                $this->repositoryOwner,
                $this->repositoryName,
                $pullRequest->getNumber(),
                'Merged by JirHub',
                $pullRequest->getHeadSha(),
                $mergeMethod,
                $pullRequest->getTitle()
            );
        } catch (\Exception $e) {
            $this->eventDispatcher->dispatch(PullRequestMergeFailureEvent::NAME, new PullRequestMergeFailureEvent($pullRequest, $e->getMessage()));
        }

        $this->eventDispatcher->dispatch(PullRequestMergedEvent::NAME, new PullRequestMergedEvent($pullRequest));
    }
}
