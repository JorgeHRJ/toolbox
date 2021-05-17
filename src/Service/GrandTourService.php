<?php

namespace App\Service;

use App\Entity\GrandTour;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\GrandTourRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class GrandTourService extends BaseService
{
    private GrandTourRepository $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(GrandTour::class);
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
