<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @param User $user
     * @param string $startDate
     * @param string $endDate
     * @return Task[]|array
     */
    public function getBetweenDates(User $user, string $startDate, string $endDate): array
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->where('t.date >= :startDate')
            ->andWhere('t.date <= :endDate')
            ->andWhere('t.user = :userId')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('userId', $user->getId());

        return $qb->getQuery()->getResult();
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
