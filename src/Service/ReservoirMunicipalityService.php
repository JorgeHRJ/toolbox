<?php

namespace App\Service;

use App\Entity\ReservoirMunicipality;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\ReservoirMunicipalityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ReservoirMunicipalityService extends BaseService
{
    /** @var ReservoirMunicipalityRepository */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(ReservoirMunicipality::class);
    }

    /**
     * @param string $name
     * @return ReservoirMunicipality
     * @throws \Exception
     */
    public function new(string $name): ReservoirMunicipality
    {
        $municipality = new ReservoirMunicipality();
        $municipality->setName($name);

        return $this->create($municipality);
    }

    /**
     * @return string[]|array
     */
    public function getNames(): array
    {
        return $this->repository->getNames();
    }

    /**
     * @param string $name
     * @return ReservoirMunicipality|null
     */
    public function getByName(string $name): ?ReservoirMunicipality
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
