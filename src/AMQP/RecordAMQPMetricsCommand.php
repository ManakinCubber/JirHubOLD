<?php

namespace App\AMQP;

use App\AMQP\Repository\AMQPQueueMetricsRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecordAMQPMetricsCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'record:amqp-metrics';

    private AMQPQueueMetricsRepository $AMQPQueueMetricsRepository;

    public function __construct(AMQPQueueMetricsRepository $AMQPQueueMetricsRepository)
    {
        parent::__construct();

        $this->AMQPQueueMetricsRepository = $AMQPQueueMetricsRepository;
    }

    protected function configure()
    {
        $this->setDescription('Record all AMQP queues metrics');
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->AMQPQueueMetricsRepository->getQueuesMetrics();
    }
}
