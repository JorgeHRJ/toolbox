<?php

namespace App\Service;

use App\Entity\TaskTag;
use App\Entity\User;
use App\Repository\TaskTagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class TaskTagService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var LoggerInterface */
    private $logger;

    /** @var TaskTagRepository */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->repository = $this->entityManager->getRepository(TaskTag::class);
    }

    /**
     * @param User $user
     * @param string $name
     * @return TaskTag|null
     */
    public function getByName(User $user, string $name): ?TaskTag
    {
        return $this->repository->findOneBy(['user' => $user, 'name' => $name]);
    }

    /**
     * @param User $user
     * @return TaskTag[]|array
     */
    public function getByUser(User $user): array
    {
        return $this->repository->findBy(['user' => $user]);
    }
}
