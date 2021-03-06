<?php

namespace App\Library\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

abstract class BaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entity)
    {
        parent::__construct($registry, $entity);
    }

    abstract public function getFilterFields(): array;

    /**
     * @param User|null $user
     * @param string|null $filter
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return mixed
     */
    public function getAll(
        ?User $user,
        string $filter = null,
        array $orderBy = null,
        int $limit = null,
        int $offset = null
    ) {
        $alias = 't';
        $qb = $this->createQueryBuilder($alias);

        $this->setFilter($qb, $alias, $filter);
        if ($user instanceof User) {
            $this->setUserRestriction($user, $qb, $alias);
        }

        if (!empty($orderBy)) {
            foreach ($orderBy as $field => $dir) {
                $qb->orderBy(sprintf('%s.%s', $alias, $field), $dir);
            }
        }

        if ($limit !== null && $offset !== null) {
            $qb->setFirstResult($offset);
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->execute();
    }

    /**
     * @param User|null $user
     * @param string|null $filter
     *
     * @return int
     */
    public function getAllCount(?User $user, ?string $filter)
    {
        $alias = 't';

        $qb = $this->createQueryBuilder($alias)->select(sprintf('count(%s.id)', $alias));

        $this->setFilter($qb, $alias, $filter);
        if ($user instanceof User) {
            $this->setUserRestriction($user, $qb, $alias);
        }

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     * @param string $alias
     * @param QueryBuilder $qb
     * @param string|null $filter
     */
    protected function setFilter(QueryBuilder $qb, string $alias, ?string $filter): void
    {
        if (!$filter) {
            return;
        }

        $filterFields = $this->getFilterFields();
        foreach ($filterFields as $field) {
            $qb->orWhere(sprintf('%s LIKE :%s_filter', sprintf('%s.%s', $alias, $field), $field));
            $qb->setParameter(sprintf('%s_filter', $field), sprintf('%%%s%%', $filter));
        }
    }

    /**
     * @param User $user
     * @param string $alias
     * @param QueryBuilder $qb
     */
    protected function setUserRestriction(User $user, QueryBuilder $qb, string $alias): void
    {
        $qb->andWhere(sprintf('%s.user = :userId', $alias));
        $qb->setParameter('userId', $user->getId());
    }
}
