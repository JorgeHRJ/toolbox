<?php

namespace App\Repository;

use App\Entity\Classic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Classic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Classic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Classic[]    findAll()
 * @method Classic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Classic::class);
    }

    // /**
    //  * @return Classic[] Returns an array of Classic objects
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
    public function findOneBySomeField($value): ?Classic
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
