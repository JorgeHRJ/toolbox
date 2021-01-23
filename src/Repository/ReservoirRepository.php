<?php

namespace App\Repository;

use App\Entity\Reservoir;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservoirRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservoir::class);
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
