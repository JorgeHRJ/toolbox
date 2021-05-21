<?php

namespace App\Repository;

use App\Entity\Classic;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClassicRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Classic::class);
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
