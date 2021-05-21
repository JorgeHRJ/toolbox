<?php

namespace App\Repository;

use App\Entity\StageUser;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class StageUserRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StageUser::class);
    }

    public function getFilterFields(): array
    {
        return [];
    }

    public function getByUserRaceSlugAndNumber(User $user, string $raceSlug, int $number): ?StageUser
    {
        $queryBuilder = $this->createQueryBuilder('su');
        $queryBuilder
            ->join('su.stage', 's')
            ->join('s.race', 'r')
            ->addSelect('s')
            ->addSelect('r')
            ->andWhere('su.user = :userId')
            ->andWhere('r.slug = :raceSlug')
            ->andWhere('s.number = :number')
            ->setParameter('userId', $user->getId())
            ->setParameter('raceSlug', $raceSlug)
            ->setParameter('number', $number);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
