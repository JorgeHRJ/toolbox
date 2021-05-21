<?php

namespace App\Service;

use App\Entity\StageUser;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\StageUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class StageUserService extends BaseService
{
    private StageUserRepository $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(StageUser::class);
    }

    public function getByUserRaceSlugAndNumber(User $user, string $raceSlug, int $number): ?StageUser
    {
        return $this->repository->getByUserRaceSlugAndNumber($user, $raceSlug, $number);
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
