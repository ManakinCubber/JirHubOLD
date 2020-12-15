<?php

namespace App\TMA;

use App\Repository\Jira\JiraFilterRepository;
use App\TMA\Repository\TMAIssueRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecordTMAMetricsCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'record:tma-metrics';

    private JiraFilterRepository $jiraFilterRepository;
    private TMAIssueRepository $tmaIssueRepository;
    private LoggerInterface $logger;
    private int $tmaFilterId;

    public function __construct(
        JiraFilterRepository $jiraFilterRepository,
        TMAIssueRepository $tmaIssueRepository,
        LoggerInterface $logger,
        int $tmaFilterId
    ) {
        parent::__construct();

        $this->logger               = $logger;
        $this->jiraFilterRepository = $jiraFilterRepository;
        $this->tmaIssueRepository   = $tmaIssueRepository;
        $this->tmaFilterId          = $tmaFilterId;
    }

    protected function configure()
    {
        $this->setDescription('Record all tasks in TMA\'s Jira Filter');
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $jiraFilter = $this->jiraFilterRepository->find($this->tmaFilterId);
        $dateTime   = new \DateTimeImmutable();

        foreach ($jiraFilter->getIssues() as $issue) {
            $this->tmaIssueRepository->save($issue, $dateTime);
        }
    }
}
