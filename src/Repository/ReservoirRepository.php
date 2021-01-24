<?php

namespace App\Repository;

use App\Entity\Reservoir;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservoirRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservoir::class);
    }

    /**
     * @param int $id
     * @return Reservoir|null
     */
    public function getWithData(int $id): ?Reservoir
    {
        $qb = $this->createQueryBuilder('r');
        $qb
            ->select('r, rm, rd, rp')
            ->join('r.municipality', 'rm')
            ->join('r.data', 'rd')
            ->join('rd.process', 'rp')
            ->where('r.id = :reservoirId')
            ->setParameter('reservoirId', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
