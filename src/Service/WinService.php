<?php

namespace App\Service;

use App\Entity\Win;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\WinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class WinService extends BaseService
{
    private WinRepository $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(Win::class);
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
