<?php

namespace App\Repository;

use App\Entity\Team;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class TeamRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
