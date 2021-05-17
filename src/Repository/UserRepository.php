<?php

namespace App\Repository;

use App\Entity\User;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getByRole(string $role): array
    {
        $queryBuilder = $this->createQueryBuilder('u');
        $queryBuilder
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', $role);

        return $queryBuilder->getQuery()->getResult();
    }

    public function getByRoles(array $roles): array
    {
        $queryBuilder = $this->createQueryBuilder('u');
        foreach ($roles as $role) {
            $queryBuilder
                ->orWhere(sprintf('u.roles LIKE :role_%s', strtolower($role)))
                ->setParameter(sprintf('role_%s', strtolower($role)), sprintf('%%%s%%', $role));
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
