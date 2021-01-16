<?php

namespace App\Repository;

use App\Entity\TransactionCategory;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class TransactionCategoryRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransactionCategory::class);
    }

    /**
     * @param User $user
     * @param int $type
     * @param string $month
     * @param string $year
     * @return TransactionCategory[]|array
     */
    public function findByTypeMonthAndYear(User $user, int $type, string $year, string $month): array
    {
        $qb = $this->createQueryBuilder('tc');
        $qb
            ->select('tc')
            ->addSelect('tm')
            ->addSelect('t')
            ->leftJoin('tc.months', 'tm', 'WITH', 'tm.month = :month AND tm.year = :year')
            ->leftJoin('tm.transactions', 't')
            ->where('tc.user = :userId')
            ->andWhere('tc.type = :type')
            ->setParameter('userId', $user->getId())
            ->setParameter('type', $type)
            ->setParameter('month', $month)
            ->setParameter('year', $year);

        return $qb->getQuery()->getResult();
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
