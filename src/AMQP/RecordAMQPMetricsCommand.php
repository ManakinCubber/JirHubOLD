<?php

namespace App\AMQP;

use App\AMQP\Repository\AMQPQueueMetricsRepository;
use Elasticsearch\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecordAMQPMetricsCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'record:amqp-metrics';

    private AMQPQueueMetricsRepository $AMQPQueueMetricsRepository;
    private Client $elasticsearchClient;
    private string $index;

    public function __construct(AMQPQueueMetricsRepository $AMQPQueueMetricsRepository, Client $elasticsearchClient, string $index)
    {
        parent::__construct();

        $this->AMQPQueueMetricsRepository = $AMQPQueueMetricsRepository;
        $this->elasticsearchClient        = $elasticsearchClient;
        $this->index                      = $index;
    }

    protected function configure()
    {
        $this->setDescription('Record all AMQP queues metrics');
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->elasticsearchClient->create([
            'id'    => uniqid(),
            'index' => $this->index,
            'body'  => $this->AMQPQueueMetricsRepository->getQueuesMetrics(),
        ]);
    }
}
