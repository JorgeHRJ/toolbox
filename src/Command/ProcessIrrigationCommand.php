<?php

namespace App\Command;

use App\Service\Irrigation\IrrigationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessIrrigationCommand extends Command
{
    const COMMAND_NAME = 'toolbox:irrigation:process';

    private IrrigationService $irrigationService;
    private LoggerInterface $logger;

    public function __construct(IrrigationService $irrigationService, LoggerInterface $logger)
    {
        $this->irrigationService = $irrigationService;
        $this->logger = $logger;
        parent::__construct(self::COMMAND_NAME);
    }

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Process irrigations data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->info(sprintf('Irrigations - Started to process. Date %s', date('d-m-Y')));

        $this->irrigationService->process();

        return Command::SUCCESS;
    }
}
