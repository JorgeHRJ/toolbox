<?php

namespace App\Repository;

use App\Entity\GrandTourParticipation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GrandTourParticipation|null find($id, $lockMode = null, $lockVersion = null)
 * @method GrandTourParticipation|null findOneBy(array $criteria, array $orderBy = null)
 * @method GrandTourParticipation[]    findAll()
 * @method GrandTourParticipation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GrandTourParticipationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GrandTourParticipation::class);
    }

    // /**
    //  * @return GrandTourParticipation[] Returns an array of GrandTourParticipation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GrandTourParticipation
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
