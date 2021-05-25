<?php

namespace App\Service;

use App\Entity\IrrigationData;
use App\Entity\IrrigationStat;
use App\Entity\IrrigationZone;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\IrrigationDataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class IrrigationDataService extends BaseService
{
    private StorageService $storageService;
    private IrrigationDataRepository $repository;

    public function __construct(
        StorageService $storageService,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        parent::__construct($entityManager, $logger);
        $this->storageService = $storageService;
        $this->repository = $entityManager->getRepository(IrrigationData::class);
    }

    public function getStartDates(): array
    {
        return $this->repository->getStartDates();
    }

    public function prepareZoneData(IrrigationZone $zone): array
    {
        $result = [];

        /** @var IrrigationData $data */
        foreach ($zone->getData() as $data) {
            /** @var IrrigationStat $stat */
            foreach ($data->getStats() as $stat) {
                $result[$data->getId()][$stat->getType()][$stat->getContext()] = $stat->getValue();
            }
        }

        return $result;
    }

    public function getFilenamePath(IrrigationData $data): string
    {
        return sprintf(
            '%s/%s/%s',
            $this->storageService->getStorageFolder(),
            StorageService::IRRIGATION_FOLDER,
            $data->getFilename()
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
