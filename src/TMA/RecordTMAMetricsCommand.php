<?php

namespace App\TMA;

use App\Repository\Jira\JiraFilterRepository;
use App\TMA\Repository\TMAIssueRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'record:tma-metrics')]
class RecordTMAMetricsCommand extends Command
{
    public function __construct(
        private readonly JiraFilterRepository $jiraFilterRepository,
        private readonly TMAIssueRepository $tmaIssueRepository,
        private readonly int $tmaFilterId
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Record all tasks in TMA\'s Jira Filter');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $jiraFilter = $this->jiraFilterRepository->find($this->tmaFilterId);
        $dateTime = new \DateTimeImmutable();

        foreach ($jiraFilter->getIssues() as $issue) {
            $this->tmaIssueRepository->save($issue, $dateTime);
        }

        return 0;
    }
}
