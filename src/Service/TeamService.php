<?php

namespace App\Service;

use App\Entity\Team;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class TeamService extends BaseService
{
    private TeamRepository $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(Team::class);
    }

    public function getByName(string $name): ?Team
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
