<?php

namespace App\Command;

use App\Entity\ReservoirMunicipality;
use App\Service\ReservoirMunicipalityService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateReservoirMunicipalitiesCommand extends Command
{
    private $municipalityService;

    public function __construct(ReservoirMunicipalityService $municipalityService)
    {
        $this->municipalityService = $municipalityService;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('toolbox:reservoir:generate-municipalities')
            ->setDescription('Generate reservoir municipalities');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        foreach (ReservoirMunicipality::LA_PALMA_MUNICIPALITES as $municipalityName) {
            try {
                $this->municipalityService->new($municipalityName);
            } catch (\Exception $e) {
                $style->error(sprintf('There was an error: %s', $e->getMessage()));

                return Command::FAILURE;
            }
        }

        $style->success('Municipalities created!');

        return Command::SUCCESS;
    }
}
