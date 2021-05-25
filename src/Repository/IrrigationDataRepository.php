<?php

namespace App\Repository;

use App\Entity\IrrigationData;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class IrrigationDataRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IrrigationData::class);
    }

    public function getStartDates(): array
    {
        $queryBuilder = $this->createQueryBuilder('id');
        $queryBuilder
            ->select('id.startDate');

        $result = $queryBuilder->getQuery()->getArrayResult();
        return array_map(function (array $item) {
            return $item['startDate'];
        }, $result);
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
