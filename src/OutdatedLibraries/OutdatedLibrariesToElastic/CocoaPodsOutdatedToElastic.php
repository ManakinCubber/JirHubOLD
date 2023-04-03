<?php

namespace App\OutdatedLibraries\OutdatedLibrariesToElastic;

use App\OutdatedLibraries\OutdatedLibrariesToElastic\ElasticInput\CocoaPodsOutdated;
use Elastic\Elasticsearch\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'CocoaPods:outdated-libraries')]
class CocoaPodsOutdatedToElastic extends Command
{
    public function __construct(
        private readonly CocoaPodsOutdated $CocoaPodsOutdated,
        private readonly Client $elasticsearchClient
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('sending CocoaPods outdated libraries to elasticsearsh');
        $this->addArgument('path', InputArgument::REQUIRED, 'a path to your txt file is required');
        $this->addArgument('name', InputArgument::REQUIRED, 'the name of your project is required');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');

        if ($this->verifyNameCocoaPods($name)) {
            $json = $this->CocoaPodsOutdated->getCocoaPodsJson($input->getArgument('path'), $name);
            $params = ['index' => 'tiime-chronos-outdated-libraries', 'body' => $json];

            $this->elasticsearchClient->index($params);

            return 0;
        } else {
            throw new \LogicException('Make sure the name of your project is in "Accounts IOS, Invoice IOS".');

            return 1;
        }
    }

    private function verifyNameCocoaPods(string $name): bool
    {
        if ('Accounts IOS' === $name || 'Invoice IOS' === $name) {
            return true;
        }

        return false;
    }
}
