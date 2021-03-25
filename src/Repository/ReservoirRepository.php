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
     * @return Reservoir[]|array
     */
    public function getAllWithData(): array
    {
        $qb = $this->createQueryBuilder('r');
        $qb
            ->select('r, rd, rm, rp')
            ->join('r.data', 'rd')
            ->join('r.municipality', 'rm')
            ->join('rd.process', 'rp')
            ->orderBy('rp.date', 'DESC');

        return $qb->getQuery()->getResult();
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
            ->setParameter('reservoirId', $id)
            ->orderBy('rp.date', 'DESC');

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
