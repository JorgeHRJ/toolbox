<?php

namespace App\Repository;

use App\Entity\ReservoirData;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservoirDataRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservoirData::class);
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
