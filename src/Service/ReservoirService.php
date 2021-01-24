<?php

namespace App\Service;

use App\Entity\Reservoir;
use App\Entity\ReservoirMunicipality;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\ReservoirRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ReservoirService extends BaseService
{
    /** @var ReservoirRepository */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(Reservoir::class);
    }

    /**
     * @param ReservoirMunicipality $municipality
     * @param string $reservoirName
     * @param int $capacity
     * @return Reservoir
     * @throws \Exception
     */
    public function new(
        ReservoirMunicipality $municipality,
        string $reservoirName,
        int $capacity
    ): Reservoir {
        $reservoir = new Reservoir();
        $reservoir->setName($reservoirName);
        $reservoir->setCapacity($capacity);
        $reservoir->setMunicipality($municipality);

        return $this->create($reservoir);
    }

    /**
     * @param int $id
     * @return Reservoir|null
     */
    public function getWithData(int $id): ?Reservoir
    {
        return $this->repository->getWithData($id);
    }

    /**
     * @param string $name
     * @return Reservoir|null
     */
    public function getByName(string $name): ?Reservoir
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
