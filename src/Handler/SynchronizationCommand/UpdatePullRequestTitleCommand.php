<?php

namespace App\Handler\SynchronizationCommand;

use App\Model\JirHubTask;
use App\Repository\GitHub\Constant\PullRequestUpdatableFields;
use App\Repository\GitHub\PullRequestRepository;
use Psr\Log\LoggerInterface;

final class UpdatePullRequestTitleCommand implements SynchronizationCommandInterface
{
    /** @var PullRequestRepository */
    private $pullRequestRepository;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        PullRequestRepository $pullRequestRepository,
        LoggerInterface $logger
    ) {
        $this->pullRequestRepository = $pullRequestRepository;
        $this->logger                = $logger;
    }

    public function execute(JirHubTask $jirHubTask): void
    {
        if (null !== $jirHubTask->getJiraIssue()) {
            return;
        }

        $pullRequest = $jirHubTask->getGithubPullRequest();
        $title       = $pullRequest->getTitle();

        $regexPattern  = '/^\[(?<prefix>.*)\]/i';
        $betterPrTitle = null;

        $matches = [];
        preg_match($regexPattern, $title, $matches);

        $labels = [
            'Tech' => 'Tech',
            'bug'  => 'Fix',
        ];

        foreach ($labels as $label => $prefix) {
            if ($pullRequest->hasLabel($label) && empty($matches['prefix'])) {
                $betterPrTitle = sprintf('[%s] %s', $prefix, $title);
            } elseif (!$pullRequest->hasLabel($label) && $matches['prefix'] === $prefix) {
                $this->pullRequestLabelRepository->create(
                    $pullRequest,
                    $label
                );
    
                $this->logger->info(
                    sprintf(
                        'Added label %s to pull request #%d',
                        $label,
                        $pullRequest->getId()
                    )
                );
            }
        }

        if (null !== $betterPrTitle) {
            $this->pullRequestRepository->update(
                $pullRequest,
                [PullRequestUpdatableFields::TITLE => $betterPrTitle]
            );

            $this->logger->info(
                sprintf('Updated pull request #%d title', $jirHubTask->getGithubPullRequest()->getId())
            );
        }
    }
}
