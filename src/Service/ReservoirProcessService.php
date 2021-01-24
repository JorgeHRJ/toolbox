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
    private $storageService;

    /** @var ReservoirProcessRepository */
    private $repository;

    public function __construct(
        StorageService $storageService,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        parent::__construct($entityManager, $logger);
        $this->storageService = $storageService;
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

    public function getProcessedDates(): array
    {
        return $this->repository->getProcessedDates();
    }

    /**
     * @param ReservoirProcess $process
     * @return string
     */
    public function getFilenamePath(ReservoirProcess $process): string
    {
        return sprintf(
            '%s/%s/%s',
            $this->storageService->getStorageFolder(),
            StorageService::RESERVOIR_FOLDER,
            $process->getFilename()
        );
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
