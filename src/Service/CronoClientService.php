<?php

namespace App\Service;

use App\Entity\CronoClient;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\CronoClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class CronoClientService extends BaseService
{
    private CronoClientRepository $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(CronoClient::class);
    }

    public function getForChoices(User $user): array
    {
        $clients = $this->repository->getIdsNames($user);

        $choices = [];
        foreach ($clients as $client) {
            $choices[$client['name']] = $client['id'];
        }

        return $choices;
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
