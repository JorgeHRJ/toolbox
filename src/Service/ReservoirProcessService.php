<?php

namespace App\Service;

use App\Entity\ReservoirProcess;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\ReservoirProcessRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ReservoirProcessService extends BaseService
{
    /** @var ReservoirProcessRepository */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(ReservoirProcess::class);
    }

    public function new(string $filename, string $date): ReservoirProcess
    {
        $process = new ReservoirProcess();
        $process->setStatus(ReservoirProcess::PENDING_STATUS);
        $process->setDate(\DateTime::createFromFormat('d/m/Y', $date));
        $process->setFilename($filename);

        return $this->create($process);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function getProcessedDates(): array
    {
        return $this->repository->getProcessedDates();
    }

    public function getSortFields(): array
    {
        return [];
    }

    public function getRepository(): BaseRepository
    {
        return $this->repository;
    }
}
