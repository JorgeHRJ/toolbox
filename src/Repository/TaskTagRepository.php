<?php

namespace App\Repository;

use App\Entity\TaskTag;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class TaskTagRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskTag::class);
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
