<?php

namespace App\Repository;

use App\Entity\CronoPlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CronoPlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method CronoPlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method CronoPlan[]    findAll()
 * @method CronoPlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CronoPlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CronoPlan::class);
    }

    // /**
    //  * @return CronoPlan[] Returns an array of CronoPlan objects
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
    public function findOneBySomeField($value): ?CronoPlan
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
