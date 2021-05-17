<?php

namespace App\Repository;

use App\Entity\Win;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class WinRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Win::class);
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
