<?php

namespace App\Service;

use App\Entity\Asset;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\AssetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class AssetService extends BaseService
{
    private AssetRepository $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(Asset::class);
    }

    public function getSortFields(): array
    {
        return [];
    }

    public function getRepository(): BaseRepository
    {
        return $this->repository;
    }
}
