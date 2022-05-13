<?php

namespace App\AMQP;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecordAMQPMetricsCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'record:amqp-metrics';

    public function __construct(
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Record all AMQP queues metrics');
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {

    }
}
