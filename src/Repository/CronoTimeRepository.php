<?php

namespace App\Repository;

use App\Entity\CronoTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CronoTime|null find($id, $lockMode = null, $lockVersion = null)
 * @method CronoTime|null findOneBy(array $criteria, array $orderBy = null)
 * @method CronoTime[]    findAll()
 * @method CronoTime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CronoTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CronoTime::class);
    }

    // /**
    //  * @return CronoTime[] Returns an array of CronoTime objects
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
    public function findOneBySomeField($value): ?CronoTime
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
