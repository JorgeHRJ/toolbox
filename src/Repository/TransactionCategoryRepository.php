<?php

namespace App\Repository;

use App\Entity\TransactionCategory;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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
     * @param int $month
     * @param int $year
     * @return TransactionCategory[]|array
     */
    public function findByTypeMonthAndYear(User $user, int $type, int $year, int $month): array
    {
        $qb = $this->createQueryBuilder('tc');
        $qb
            ->select('tc')
            ->addSelect('tm')
            ->addSelect('t')
            ->join('tc.months', 'tm', 'WITH', 'tm.month = :month AND tm.year = :year')
            ->leftJoin('tm.transactions', 't')
            ->where('tc.user = :userId')
            ->andWhere('tc.type = :type')
            ->setParameter('userId', $user->getId())
            ->setParameter('type', $type)
            ->setParameter('month', $month)
            ->setParameter('year', $year);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $year
     * @param string $month
     * @return TransactionCategory[]|array
     */
    public function findMonthlyFromPreviousMonth(string $year, string $month): array
    {
        $qb = $this->createQueryBuilder('tc');
        $qb
            ->select('tc')
            ->addSelect('tm')
            ->join('tc.months', 'tm', 'WITH', 'tm.month = :month AND tm.year = :year')
            ->where('tc.periodicity = :monthly')
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->setParameter('monthly', TransactionCategory::MONTHLY_PERIDIOCITY);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param User $user
     * @param int $type
     * @param int $year
     * @param int $month
     * @return float
     */
    public function getTypeTotalValuesFromMonthYear(User $user, int $type, int $year, int $month): float
    {
        $qb = $this->createQueryBuilder('tc');
        $qb
            ->select('SUM(tm.value)')
            ->join('tc.months', 'tm')
            ->where('tc.user = :userId')
            ->andWhere('tc.type = :type')
            ->andWhere('tm.year = :year')
            ->andWhere('tm.month = :month')
            ->setParameter('userId', $user->getId())
            ->setParameter('type', $type)
            ->setParameter('year', $year)
            ->setParameter('month', $month);

        try {
            $result = $qb->getQuery()->getSingleResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
            return $result !== null ? $result : 0.00;
        } catch (NoResultException | NonUniqueResultException $e) {
            return 0.00;
        }
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
