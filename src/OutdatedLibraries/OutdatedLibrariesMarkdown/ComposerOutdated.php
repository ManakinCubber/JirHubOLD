<?php

namespace App\OutdatedLibraries\OutdatedLibrariesMarkdown;

use App\OutdatedLibraries\OutdatedFileToTable\OutdatedFileToTable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'collect:composer-outdated-libraries')]
class ComposerOutdated extends Command
{
    use PatternTrait;

    public function __construct(private readonly OutdatedFileToTable $OutdatedFileToTable)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('collecting outdated libraries');
        $this->addArgument('path', InputArgument::REQUIRED, 'a path to your json file is required');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');
        $tab = $this->OutdatedFileToTable->composerOutdatedTable($path);

        foreach ($tab as $key => $value) {
            $tab[$key] = $this->patternLigne($value);
        }
        $output->writeln(array_merge($this->generateHeader('chronos (API)'), $tab));

        return 0;
    }
}
