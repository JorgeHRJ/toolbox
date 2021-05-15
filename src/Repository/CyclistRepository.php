<?php

namespace App\Repository;

use App\Entity\Cyclist;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class CyclistRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cyclist::class);
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
