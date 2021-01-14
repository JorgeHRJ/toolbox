<?php

namespace App\Repository;

use App\Entity\TransactionMonth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TransactionMonth|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransactionMonth|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransactionMonth[]    findAll()
 * @method TransactionMonth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionMonthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransactionMonth::class);
    }

    // /**
    //  * @return TransactionMonth[] Returns an array of TransactionMonth objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TransactionMonth
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
