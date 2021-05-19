<?php

namespace App\Repository;

use App\Entity\Race;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class RaceRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Race::class);
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
