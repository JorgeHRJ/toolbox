<?php

namespace App\Repository;

use App\Entity\IrrigationStat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IrrigationStat|null find($id, $lockMode = null, $lockVersion = null)
 * @method IrrigationStat|null findOneBy(array $criteria, array $orderBy = null)
 * @method IrrigationStat[]    findAll()
 * @method IrrigationStat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IrrigationStatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IrrigationStat::class);
    }

    // /**
    //  * @return IrrigationStat[] Returns an array of IrrigationStat objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IrrigationStat
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
