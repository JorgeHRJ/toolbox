<?php

namespace App\Repository;

use App\Entity\Cyclist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cyclist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cyclist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cyclist[]    findAll()
 * @method Cyclist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CyclistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cyclist::class);
    }

    // /**
    //  * @return Cyclist[] Returns an array of Cyclist objects
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
    public function findOneBySomeField($value): ?Cyclist
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
