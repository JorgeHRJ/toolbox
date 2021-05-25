<?php

namespace App\Repository;

use App\Entity\IrrigationProcess;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class IrrigationProcessRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IrrigationProcess::class);
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
