<?php

namespace App\Repository;

use App\Entity\IrrigationZone;
use App\Library\Model\Partial\IrrigationZonePartial;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class IrrigationZoneRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IrrigationZone::class);
    }

    public function getByName(string $name): ?IrrigationZone
    {
        $queryBuilder = $this->createQueryBuilder('iz');
        $queryBuilder
            ->andWhere('iz.name = :name')
            ->setParameter('name', $name);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return IrrigationZonePartial[]
     */
    public function getPartialZones(): array
    {
        $queryBuilder = $this->createQueryBuilder('iz');
        $queryBuilder
            ->select(sprintf('NEW %s(iz.id, iz.name, id.startDate, id.endDate)', IrrigationZonePartial::class))
            ->join('iz.data', 'id')
            ->groupBy('iz.name')
            ->orderBy('id.startDate', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }

    public function getDataById(int $zoneId): ?IrrigationZone
    {
        $queryBuilder = $this->createQueryBuilder('iz');
        $queryBuilder
            ->join('iz.data', 'id')
            ->addSelect('id')
            ->andWhere('iz.id = :zoneId')
            ->setParameter('zoneId', $zoneId);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
