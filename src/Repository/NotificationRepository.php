<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class NotificationRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function countUnread(User $user): int
    {
        $queryBuilder = $this->createQueryBuilder('n');
        $queryBuilder
            ->select('COUNT(n.id)')
            ->andWhere('n.user = :userId')
            ->andWhere('n.status = :unreadStatus')
            ->setParameter('userId', $user->getId())
            ->setParameter('unreadStatus', Notification::UNREAD_STATUS);

        try {
            return $queryBuilder->getQuery()->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            return 0;
        }
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
