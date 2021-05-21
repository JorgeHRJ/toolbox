<?php

namespace App\Service;

use App\Entity\Race;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\RaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class RaceService extends BaseService
{
    private RaceRepository $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(Race::class);
    }

    public function getByName(string $name): ?Race
    {
        return $this->repository->findOneBy(['name' => $name]);
    }

    public function getByNameAndYear(string $name, string $year): ?Race
    {
        return $this->repository->findOneBy(['name' => $name, 'year' => $year]);
    }

    public function getBySlug(string $slug): ?Race
    {
        return $this->repository->findOneBy(['slug' => $slug]);
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
