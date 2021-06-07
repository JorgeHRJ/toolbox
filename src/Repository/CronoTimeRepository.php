<?php

namespace App\Repository;

use App\Entity\CronoTime;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class CronoTimeRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CronoTime::class);
    }

    /**
     * @param User $user
     * @param string $startDate
     * @param string $endDate
     * @return CronoTime[]
     */
    public function getBetweenDates(User $user, string $startDate, string $endDate): array
    {
        $queryBuilder = $this->createQueryBuilder('ct');
        $queryBuilder
            ->join('ct.month', 'cm')
            ->join('ct.client', 'cc')
            ->addSelect('cc')
            ->andWhere('ct.startAt >= :startDate')
            ->andWhere('ct.endAt <= :endDate')
            ->andWhere('cm.user = :userId')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('userId', $user->getId());

        return $queryBuilder->getQuery()->getResult();
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
