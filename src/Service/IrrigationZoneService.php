<?php

namespace App\Service;

use App\Entity\IrrigationZone;
use App\Library\Model\Partial\IrrigationZonePartial;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\IrrigationZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class IrrigationZoneService extends BaseService
{
    private IrrigationZoneRepository $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(IrrigationZone::class);
    }

    /**
     * @return IrrigationZonePartial[]
     */
    public function getPartialZones(): array
    {
        return $this->repository->getPartialZones();
    }

    public function getByName(string $name): ?IrrigationZone
    {
        return $this->repository->getByName($name);
    }

    public function getDataById(int $zoneId): ?IrrigationZone
    {
        return $this->repository->getDataById($zoneId);
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
