<?php

namespace App\Service;

use App\Entity\Cyclist;
use App\Entity\CyclistRace;
use App\Entity\Race;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\CyclistRaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class CyclistRaceService extends BaseService
{
    private CyclistRaceRepository $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(CyclistRace::class);
    }

    public function getByUserCyclistRace(User $user, Cyclist $cyclist, Race $race): ?CyclistRace
    {
        return $this->repository->findOneBy(['user' => $user, 'cyclist' => $cyclist, 'race' => $race]);
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
