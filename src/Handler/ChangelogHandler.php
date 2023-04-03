<?php

namespace App\Handler;

use App\Repository\GitHub\CommitRepository;
use App\Repository\GitHub\PullRequestRepository;

readonly class ChangelogHandler
{
    public function __construct(
        private CommitRepository $commitRepository,
        private PullRequestRepository $pullRequestRepository
    ) {
    }

    public function getChangelog(string $prev_head, string $head): array
    {
        $messagesLinks = $this->getOrderedChangelog($prev_head, $head);
        $messages = [];

        foreach ($messagesLinks as $value) {
            if (\is_array($value)) {
                $messages[] = $value['message'];
            } else {
                $messages[] = $value;
            }
        }

        return $messages;
    }

    public function getChangelogWithLinks(string $prev_head, string $head): array
    {
        $type = [];
        $messagesLinks = $this->getOrderedChangelog($prev_head, $head);

        foreach ($messagesLinks as $value) {
            $type[] = \gettype($value);
        }

        return ['num' => \count($messagesLinks), 'type' => $type, 'messageLinks' => $messagesLinks];
    }

    private function getCommitsWithLinks($prev_head, $head): array
    {
        $commits = $this->commitRepository->getChangelog($prev_head, $head);
        $messagesLinks = [];

        foreach ($commits['commits'] as $commit) {
            $messagesLinks[] = ['message' => explode(PHP_EOL, $commit['commit']['message'])[0], 'html_url' => $commit['html_url']];
        }

        return $messagesLinks;
    }

    private function getOrderedChangelog($prev_head, $head): array
    {
        $messagesLinks = $this->getCommitsWithLinks($prev_head, $head);
        $messagesLinks = array_filter($messagesLinks, function ($messagesLink) {
            $prefixes = ['MEP', 'Merge branch'];

            foreach ($prefixes as $prefix) {
                if (mb_substr($messagesLink['message'], 0, mb_strlen($prefix)) === $prefix) {
                    return false;
                }
            }

            return true;
        });

        $plSections = [];
        $commits = [];

        foreach ($messagesLinks as $key => $value) {
            $commit = ['message' => trim($value['message']), 'labels' => [], 'html_url' => []];
            preg_match('/\(?#(\d+)\)?$/', $value['message'], $matches);
            $commit['html_url'] = $messagesLinks[$key]['html_url'];

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

        $messagesLinks = [];

        foreach ($plSections as $plSection) {
            $messagesLinks[] = $plSection;
            $messagesLinks[] = preg_replace('/.?/', '-', $plSection);

            foreach ($commits as $key => $commit) {
                if (\in_array($plSection, $commit['labels'])) {
                    $messagesLinks[] = ['message' => $commit['message'], 'html_url' => $commit['html_url']];

                    unset($commits[$key]);
                }
            }
            $messagesLinks[] = null;
        }

        $bugMessages = [];

        foreach ($commits as $key => $commit) {
            if (\in_array('bug', $commit['labels'])) {
                $bugMessages[] = $commit['message'];

                unset($commits[$key]);
            }
            unset($commits[$key]['labels']);
        }

        if (\count($bugMessages) > 0) {
            $messagesLinks[] = 'Bug fixes';
            $messagesLinks[] = '---------';
            $messagesLinks = array_merge($messagesLinks, $bugMessages);
            $messagesLinks[] = null;
        }

        if (\count($commits) > 0) {
            if (\count($messagesLinks) > 0) {
                $messagesLinks[] = 'Autres';
                $messagesLinks[] = '------';
            }
            $messagesLinks = array_merge($messagesLinks, $commits);
        }

        if (\count($messagesLinks) > 0 && null === $messagesLinks[\count($messagesLinks) - 1]) {
            unset($messagesLinks[\count($messagesLinks) - 1]);
        }

        return $messagesLinks;
    }

    private function _getPullRequestLabels($pullRequestId): array
    {
        $pullRequest = $this->pullRequestRepository->fetch($pullRequestId);

        return $pullRequest->getLabels();
    }
}
