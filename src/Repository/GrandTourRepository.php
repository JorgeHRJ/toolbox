<?php

namespace App\Repository;

use App\Entity\GrandTour;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class GrandTourRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GrandTour::class);
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
