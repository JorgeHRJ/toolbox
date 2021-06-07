<?php

namespace App\Repository;

use App\Entity\CronoMonth;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class CronoMonthRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CronoMonth::class);
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
