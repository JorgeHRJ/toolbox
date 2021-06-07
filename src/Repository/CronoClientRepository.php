<?php

namespace App\Repository;

use App\Entity\CronoClient;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class CronoClientRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CronoClient::class);
    }

    public function getIdsNames(User $user): array
    {
        $queryBuilder = $this->createQueryBuilder('cc');
        $queryBuilder
            ->select('cc.id, cc.name')
            ->andWhere('cc.user = :userId')
            ->setParameter('userId', $user->getId());

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
