<?php

namespace App\TMA\Normalizer;

use App\Model\JiraIssue;

final class JiraIssueNormalizer
{
    public function normalize(JiraIssue $jiraIssue, \DateTimeInterface $dateTime): array
    {
        return [
            'id' => $jiraIssue->getId(),
            '@timestamp' => $dateTime->format(\DateTimeInterface::RFC3339),
            'key' => $jiraIssue->getKey(),
            'summary' => $jiraIssue->getSummary(),
            'flagged' => $jiraIssue->isFlagged(),
            'epic_key' => $jiraIssue->getEpicKey(),
            'issue_type' => [
                'id' => $jiraIssue->getType()->getId(),
                'name' => $jiraIssue->getType()->getName(),
                'subtask' => $jiraIssue->getType()->isSubtask(),
            ],
            'priority' => $jiraIssue->getPriority(),
            'status' => $jiraIssue->getStatus()->getName(),
            'uri' => (string) $jiraIssue->getUri(),
            'created_at' => $jiraIssue->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'published_at' => $jiraIssue->getPublishedAt() instanceof \DateTimeInterface ? $jiraIssue->getPublishedAt()->format(\DateTimeInterface::ATOM) : null,
            'resolved_at' => $jiraIssue->getResolvedAt() instanceof \DateTimeInterface ? $jiraIssue->getResolvedAt()->format(\DateTimeInterface::ATOM) : null,
            'lifespan' => $this->getLifespanInMinutes($jiraIssue->getLifespan()),
        ];
    }

    private function getLifespanInMinutes(\DateInterval $dateInterval): int
    {
        return (new \DateTime())->setTimestamp(0)->add($dateInterval)->getTimestamp() / 60;
    }
}
