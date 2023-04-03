<?php

namespace App\OutdatedLibraries\OutdatedLibrariesToElastic;

use App\OutdatedLibraries\OutdatedLibrariesToElastic\ElasticInput\NpmOutdated;
use Elastic\Elasticsearch\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'npm:outdated-libraries')]
class NpmOutdatedToElastic extends Command
{
    public function __construct(
        private readonly NpmOutdated $NpmOutdated,
        private readonly Client $elasticsearchClient
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('sending npm outdated libraries to elasticsearsh');
        $this->addArgument('path', InputArgument::REQUIRED, 'a path to your txt file is required');
        $this->addArgument('name', InputArgument::REQUIRED, 'the name of your project is required');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');

        if ($this->verifyNameNpm($name)) {
            $json = $this->NpmOutdated->getNpmJson($input->getArgument('path'), $name);
            $params = ['index' => 'tiime-chronos-outdated-libraries', 'body' => $json];

            $this->elasticsearchClient->index($params);

            return 0;
        } else {
            throw new \LogicException('Make sure the name of your project is in "Expert, Apps web".');

            return 1;
        }
    }

    private function verifyNameNpm(string $name): bool
    {
        if ('Expert' === $name || 'Apps web' === $name) {
            return true;
        }

        return false;
    }
}
