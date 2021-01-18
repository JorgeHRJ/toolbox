<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class TransactionRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
