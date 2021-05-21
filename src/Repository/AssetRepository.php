<?php

namespace App\Repository;

use App\Entity\Asset;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class AssetRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Asset::class);
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
