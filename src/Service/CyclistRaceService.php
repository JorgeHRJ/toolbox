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

    /**
     * @param User $user
     * @param Race $race
     * @return CyclistRace[]
     */
    public function getByUserAndRace(User $user, Race $race): array
    {
        return $this->repository->getByUserAndRace($user, $race);
    }

    public function getByUserRaceSlugAndCyclistSlug(User $user, string $raceSlug, string $cyclistSlug): ?CyclistRace
    {
        return $this->repository->getByUserRaceSlugAndCyclistSlug($user, $raceSlug, $cyclistSlug);
    }

    public function suggest(Race $race, string $query): array
    {
        return $this->repository->suggest($race, $query);
    }

    /**
     * @param CyclistRace[] $cyclistRaces
     * @return array
     */
    public function makeTeamCentered(array $cyclistRaces): array
    {
        $data = [];
        foreach ($cyclistRaces as $cyclistRace) {
            $teamName = $cyclistRace->getCyclist()->getTeam()->getName();
            $data[$teamName][] = $cyclistRace;
        }

        return $data;
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
