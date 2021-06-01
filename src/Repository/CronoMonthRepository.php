<?php

namespace App\Repository;

use App\Entity\CronoMonth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CronoMonth|null find($id, $lockMode = null, $lockVersion = null)
 * @method CronoMonth|null findOneBy(array $criteria, array $orderBy = null)
 * @method CronoMonth[]    findAll()
 * @method CronoMonth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CronoMonthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CronoMonth::class);
    }

    // /**
    //  * @return CronoMonth[] Returns an array of CronoMonth objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CronoMonth
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
