<?php

namespace App\Command;

use App\Handler\HerokuHandler;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'heroku:manage:dynos')]
class HerokuManageCommand extends Command
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly HerokuHandler $herokuHandler
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Put down all specified heroku dynos (set quantity to 0)');

        $this
            ->addOption(
                'action',
                null,
                InputOption::VALUE_REQUIRED,
                'Action to perform on dynos.'
            )
            ->addOption(
                'apps',
                null,
                InputOption::VALUE_REQUIRED,
                'The Heroku app names that should be put down. List separated by comma.'
            )
            ->addOption(
                'types',
                null,
                InputOption::VALUE_REQUIRED,
                'The dyno types that should be put down. List separated by comma.'
            );
    }

    /**
     * @throws GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info(sprintf('%s : exécution de la commande', self::$defaultName));

        $action = $input->getOption('action');
        $appNames = explode(',', $input->getOption('apps'));
        $dynoTypes = explode(',', $input->getOption('types'));

        switch ($action) {
            case 'up':
                $this->herokuHandler->updateDynoQuantity($appNames, $dynoTypes, 1);

                break;

            case 'down':
                $this->herokuHandler->updateDynoQuantity($appNames, $dynoTypes, 0);

                break;
        }

        return 0;
    }
}
