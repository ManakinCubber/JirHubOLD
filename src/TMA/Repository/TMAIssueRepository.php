<?php

namespace App\TMA\Repository;

use App\Model\JiraIssue;
use App\TMA\Normalizer\JiraIssueNormalizer;
use Elastic\Elasticsearch\Client;

final readonly class TMAIssueRepository
{
    public function __construct(
        private Client $elasticsearchClient,
        private JiraIssueNormalizer $normalizer,
        private string $index
    ) {
    }

    public function save(JiraIssue $jiraIssue, \DateTimeInterface $dateTime): void
    {
        $this->elasticsearchClient->create([
            'id' => uniqid(),
            'index' => $this->index,
            'body' => $this->normalizer->normalize($jiraIssue, $dateTime),
        ]);
    }
}
