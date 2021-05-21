<?php

namespace App\Repository;

use App\Entity\StageAsset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StageAsset|null find($id, $lockMode = null, $lockVersion = null)
 * @method StageAsset|null findOneBy(array $criteria, array $orderBy = null)
 * @method StageAsset[]    findAll()
 * @method StageAsset[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StageAssetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StageAsset::class);
    }

    // /**
    //  * @return StageAsset[] Returns an array of StageAsset objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StageAsset
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
