<?php

namespace App\Repository;

use App\Entity\ReservoirMunicipality;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservoirMunicipalityRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservoirMunicipality::class);
    }

    /**
     * @return string[]|array
     */
    public function getNames(): array
    {
        $qb = $this->createQueryBuilder('rm');
        $qb->select('rm.name');

        $result = $qb->getQuery()->getResult();

        return array_map(function (array $data) {
            return $data['name'];
        }, $result);
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
