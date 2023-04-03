<?php

namespace App\AMQP;

use App\AMQP\Repository\AMQPQueueMetricsRepository;
use Elastic\Elasticsearch\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'record:amqp-metrics', description: 'Record all AMQP queues metrics')]
class RecordAMQPMetricsCommand extends Command
{
    public function __construct(
        private readonly AMQPQueueMetricsRepository $AMQPQueueMetricsRepository,
        private readonly Client $elasticsearchClient,
        private readonly string $index
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $params = ['body' => []];
        $metrics = $this->AMQPQueueMetricsRepository->getQueuesMetrics();
        $now = (new \DateTimeImmutable())->format(\DateTimeInterface::RFC3339);

        foreach ($metrics as $metric) {
            $params['body'][] = [
                'index' => [
                    '_index' => $this->index,
                    'op_type' => 'create',
                ],
            ];

            $metric['@timestamp'] = $now;
            $params['body'][] = $metric;
        }

        $this->elasticsearchClient->bulk($params);

        return 0;
    }
}
