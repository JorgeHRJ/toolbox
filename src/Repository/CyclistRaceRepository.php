<?php

namespace App\Repository;

use App\Entity\CyclistRace;
use App\Entity\Race;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class CyclistRaceRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CyclistRace::class);
    }

    public function getFilterFields(): array
    {
        return [];
    }

    /**
     * @param User $user
     * @param Race $race
     * @return CyclistRace[]
     */
    public function getByUserAndRace(User $user, Race $race): array
    {
        $queryBuilder = $this->createQueryBuilder('cr');
        $queryBuilder
            ->join('cr.cyclist', 'c')
            ->join('c.team', 't')
            ->addSelect('c')
            ->addSelect('t')
            ->andWhere('cr.user = :userId')
            ->andWhere('cr.race = :raceId')
            ->setParameter('userId', $user->getId())
            ->setParameter('raceId', $race->getId());

        return $queryBuilder->getQuery()->getResult();
    }

    public function getByUserRaceSlugAndCyclistSlug(User $user, string $raceSlug, string $cyclistSlug): ?CyclistRace
    {
        $queryBuilder = $this->createQueryBuilder('cr');
        $queryBuilder
            ->join('cr.cyclist', 'c')
            ->join('cr.race', 'r')
            ->join('c.team', 't')
            ->addSelect('c')
            ->addSelect('r')
            ->addSelect('t')
            ->andWhere('c.slug = :cyclistSlug')
            ->andWhere('r.slug = :raceSlug')
            ->andWhere('cr.user = :userId')
            ->setParameter('cyclistSlug', $cyclistSlug)
            ->setParameter('raceSlug', $raceSlug)
            ->setParameter('userId', $user->getId());

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
