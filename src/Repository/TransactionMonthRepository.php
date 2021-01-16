<?php

namespace App\Repository;

use App\Entity\TransactionMonth;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class TransactionMonthRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransactionMonth::class);
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
