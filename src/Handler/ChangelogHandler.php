<?php

namespace App\Handler;

use App\Repository\GitHub\CommitRepository;
use App\Repository\GitHub\PullRequestRepository;

class ChangelogHandler
{
    /** @var CommitRepository */
    private $commitRepository;

    /** @var PullRequestRepository */
    private $pullRequestRepository;

    public function __construct(CommitRepository $commitRepository, PullRequestRepository $pullRequestRepository)
    {
        $this->commitRepository      = $commitRepository;
        $this->pullRequestRepository = $pullRequestRepository;
    }

    public function getProductionChangelog(): array
    {
        return $this->getOrderedChangelog('master', 'dev');
    }

    public function getChangelog($prev_head, $head): array
    {
        $result = $this->commitRepository->getChangelog($prev_head, $head);

        $messages = array_column(
            array_column($result['commits'], 'commit'),
            'message'
        );

        return array_map(function (string $message) {
            return explode(PHP_EOL, $message)[0];
        }, $messages);
    }

    public function getOrderedChangelog($prev_head, $head): array
    {
        $messages = $this->getChangelog($prev_head, $head);

        $messages = array_filter($messages, function ($message) {
            $prefixes = ['MEP', 'Merge branch'];

            foreach ($prefixes as $prefix) {
                if (mb_substr($message, 0, mb_strlen($prefix)) === $prefix) {
                    return false;
                }
            }

            return true;
        });

        $plSections = [];
        $commits    = [];

        foreach ($messages as $message) {
            $commit = ['message' => trim($message), 'labels' => []];
            preg_match('/\(#(\d+)\)$/', $message, $matches);

            if (isset($matches[1])) {
                $commit['labels'] = $this->_getPullRequestLabels($matches[1]);

                foreach ($commit['labels'] as $label) {
                    if ('PL' === mb_substr($label, 0, 2) && !\in_array($label, $plSections)) {
                        $plSections[] = $label;
                    }
                }
            }
            $commits[] = $commit;
        }

        natsort($plSections);

        $messages = [];

        foreach ($plSections as $plSection) {
            $messages[] = $plSection;
            $messages[] = preg_replace('/.?/', '-', $plSection);

            foreach ($commits as $key => $commit) {
                if (\in_array($plSection, $commit['labels'])) {
                    $messages[] = $commit['message'];
                    unset($commits[$key]);
                }
            }
            $messages[] = null;
        }

        $bugMessages = [];

        foreach ($commits as $key => $commit) {
            if (\in_array('bug', $commit['labels'])) {
                $bugMessages[] = $commit['message'];
                unset($commits[$key]);
            }
        }

        if (\count($bugMessages) > 0) {
            $messages[] = 'Bug fixes';
            $messages[] = '---------';
            $messages   = array_merge($messages, $bugMessages);
            $messages[] = null;
        }

        if (\count($commits) > 0) {
            $messages[] = 'Autres';
            $messages[] = '------';
            $messages   = array_merge($messages, array_column($commits, 'message'));
        }

        if (\count($messages) > 0 && null === $messages[\count($messages) - 1]) {
            unset($messages[\count($messages) - 1]);
        }

        return $messages;
    }

    private function _getPullRequestLabels($pullRequestId): array
    {
        $pullRequest = $this->pullRequestRepository->fetch($pullRequestId);

        return $pullRequest->getLabels();
    }
}
