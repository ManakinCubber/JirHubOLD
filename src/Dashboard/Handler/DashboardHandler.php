<?php

namespace App\Dashboard\Handler;

use App\Dashboard\Query\PullRequestsToDeploy;
use App\Dashboard\Query\PullRequestsToMergeOnDev;
use App\Dashboard\Query\ReviewEnvironments;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class DashboardHandler
{
    public const REVIEW_ENVIRONMENTS = 'review_environments';
    public const PULL_REQUEST_TO_DEPLOY = 'pull_requests_to_deploy';
    public const PULL_REQUEST_TO_MERGE_ON_DEV = 'pull_requests_to_merge_on_dev';
    public const CACHE_KEY = 'dashboard_data';

    public function __construct(
        private readonly ReviewEnvironments $reviewEnvironments,
        private readonly PullRequestsToDeploy $pullRequestsToDeploy,
        private readonly PullRequestsToMergeOnDev $pullRequestsToMergeOnDev,
        private readonly CacheItemPoolInterface $cache
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getData()
    {
        $cacheItem = $this->cache->getItem(self::CACHE_KEY);

        if (!$cacheItem->isHit()) {
            $cacheItem->set(
                [
                    self::REVIEW_ENVIRONMENTS => $this->reviewEnvironments->fetch(),
                    self::PULL_REQUEST_TO_DEPLOY => $this->pullRequestsToDeploy->fetch(),
                    self::PULL_REQUEST_TO_MERGE_ON_DEV => $this->pullRequestsToMergeOnDev->fetch(),
                ]
            );

            $this->cache->save($cacheItem);
        }

        return $cacheItem->get();
    }
}
