<?php

namespace App\Service;

use App\Entity\User;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class UserService extends BaseService
{
    /** @var UserRepository */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $this->entityManager->getRepository(User::class);
    }

    public function getByRole(string $role): array
    {
        return $this->repository->getByRole($role);
    }

    public function getByRoles(array $roles): array
    {
        return $this->repository->getByRoles($roles);
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
