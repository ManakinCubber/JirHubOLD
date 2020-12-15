<?php

namespace App\TMA\Repository;

use App\Model\JiraIssue;
use App\TMA\Normalizer\JiraIssueNormalizer;
use Elasticsearch\Client;

final class TMAIssueRepository
{
    private Client $elasticsearchClient;
    private JiraIssueNormalizer $normalizer;
    private string $index;

    public function __construct(
        Client $elasticsearchClient,
        JiraIssueNormalizer $normalizer,
        string $index
    ) {
        $this->elasticsearchClient = $elasticsearchClient;
        $this->normalizer          = $normalizer;
        $this->index               = $index;
    }

    public function save(JiraIssue $jiraIssue, \DateTimeInterface $dateTime): void
    {
        $this->elasticsearchClient->create([
            'id'    => uniqid(),
            'index' => $this->index,
            'body'  => $this->normalizer->normalize($jiraIssue, $dateTime),
        ]);
    }
}
