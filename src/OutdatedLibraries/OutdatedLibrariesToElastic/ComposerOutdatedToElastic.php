<?php

namespace App\OutdatedLibraries\OutdatedLibrariesToElastic;

use App\OutdatedLibraries\OutdatedLibrariesToElastic\ElasticInput\ComposerOutdated;
use Elastic\Elasticsearch\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'composer:outdated-libraries')]
class ComposerOutdatedToElastic extends Command
{
    public function __construct(
        private readonly ComposerOutdated $ComposerOutdated,
        private readonly Client $elasticsearchClient
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('sending composer outdated libraries to elasticsearsh');
        $this->addArgument('path', InputArgument::REQUIRED, 'a path to your json file is required');
        $this->addArgument('name', InputArgument::REQUIRED, 'the name of your project is required');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');

        if ($this->verifyNameChronos($name)) {
            $json = $this->ComposerOutdated->getComposerJson($input->getArgument('path'), $name);
            $params = ['index' => 'tiime-chronos-outdated-libraries', 'body' => $json];
            $this->elasticsearchClient->index($params);

            return 0;
        } else {
            throw new \LogicException('Make sure the name of your project is in "Chronos".');

            return 1;
        }
    }

    private function verifyNameChronos(string $name): bool
    {
        if ('Chronos' === $name) {
            return true;
        }

        return false;
    }
}
