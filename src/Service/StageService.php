<?php

namespace App\Service;

use App\Entity\Race;
use App\Entity\Stage;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\StageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class StageService extends BaseService
{
    private StageRepository $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(Stage::class);
    }

    public function getByRace(Race $race): array
    {
        return $this->repository->getByRace($race);
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
