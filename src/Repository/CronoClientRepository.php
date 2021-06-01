<?php

namespace App\Repository;

use App\Entity\CronoClient;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class CronoClientRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CronoClient::class);
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
