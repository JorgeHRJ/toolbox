<?php

namespace App\Command;

use App\Service\ReservoirDataService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessReservoirsCommand extends Command
{
    private ReservoirDataService $dataService;
    private LoggerInterface $logger;

    public function __construct(ReservoirDataService $dataService, LoggerInterface $logger)
    {
        $this->dataService = $dataService;
        $this->logger = $logger;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('toolbox:reservoir:process')
            ->setDescription('Process reservoirs data');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->info(sprintf('Reservoirs - Started to process. Date %s', date('d-m-Y')));

        $this->dataService->processData();

        return Command::SUCCESS;
    }
}
