<?php

namespace App\Service;

use App\Entity\Cyclist;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\CyclistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class CyclistService extends BaseService
{
    private CyclistRepository $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(Cyclist::class);
    }

    public function getByName(string $name): ?Cyclist
    {
        return $this->repository->findOneBy(['name' => $name]);
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
