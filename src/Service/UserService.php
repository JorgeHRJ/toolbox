<?php

namespace App\Service;

use App\Entity\User;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService extends BaseService
{
    private UserPasswordEncoderInterface $encoder;
    private UserRepository $repository;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        UserPasswordEncoderInterface $encoder
    ) {
        parent::__construct($entityManager, $logger);
        $this->encoder = $encoder;
        $this->repository = $this->entityManager->getRepository(User::class);
    }

    public function new(User $user, string $password): User
    {
        $password = $this->encoder->encodePassword($user, $password);
        $user->setPassword($password);

        return $this->create($user);
    }

    public function getByRole(string $role): array
    {
        return $this->repository->getByRole($role);
    }

    /**
     * @param array $roles
     * @return User[]|array
     */
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
