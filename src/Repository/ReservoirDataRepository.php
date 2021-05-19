<?php

namespace App\Repository;

use App\Entity\ReservoirData;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservoirDataRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservoirData::class);
    }

    /**
     * @return ReservoirData[]|array
     */
    public function getData(): array
    {
        $qb = $this->createQueryBuilder('rd');
        $qb
            ->select('rd, r, rm, rp')
            ->join('rd.reservoir', 'r')
            ->join('r.municipality', 'rm')
            ->join('rd.process', 'rp')
            ->orderBy('rp.date', 'DESC')
            ->groupBy('r.id');

        return $qb->getQuery()->getResult();
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
