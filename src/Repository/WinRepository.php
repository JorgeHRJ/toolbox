<?php

namespace App\Repository;

use App\Entity\Win;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Win|null find($id, $lockMode = null, $lockVersion = null)
 * @method Win|null findOneBy(array $criteria, array $orderBy = null)
 * @method Win[]    findAll()
 * @method Win[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Win::class);
    }

    // /**
    //  * @return Win[] Returns an array of Win objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Win
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
