<?php

namespace App\Repository;

use App\Entity\Race;
use App\Entity\Stage;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class StageRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stage::class);
    }

    public function getByRace(Race $race): array
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder
            ->join('s.race', 'r')
            ->join('s.assets', 'sa')
            ->join('sa.asset', 'a')
            ->addSelect('r')
            ->addSelect('sa')
            ->addSelect('a')
            ->andWhere('r.id = :raceId')
            ->setParameter('raceId', $race->getId());

        return $queryBuilder->getQuery()->getResult();
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
