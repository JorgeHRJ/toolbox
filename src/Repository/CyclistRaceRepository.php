<?php

namespace App\Repository;

use App\Entity\CyclistRace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CyclistRace|null find($id, $lockMode = null, $lockVersion = null)
 * @method CyclistRace|null findOneBy(array $criteria, array $orderBy = null)
 * @method CyclistRace[]    findAll()
 * @method CyclistRace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CyclistRaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CyclistRace::class);
    }

    // /**
    //  * @return CyclistRace[] Returns an array of CyclistRace objects
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
    public function findOneBySomeField($value): ?CyclistRace
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
