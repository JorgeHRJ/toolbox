<?php

namespace App\Library\Service;

use App\Entity\Task;
use App\Entity\TaskTag;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Psr\Log\LoggerInterface;

abstract class BaseService
{
    const ENTITIES = [Task::class, TaskTag::class];

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var LoggerInterface */
    protected $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    abstract public function getSortFields(): array;
    abstract public function getRepository(): BaseRepository;

    /**
     * @param object $entity
     * @return object
     * @throws \Exception
     */
    public function create($entity)
    {
        try {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            $this->logger->info(sprintf('Created %s ID::%s', get_class($entity), $entity->getId()));

            return $entity;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error creating %s. Error: %s', get_class($entity), $e->getMessage()));

            throw $e;
        }
    }

    /**
     * @param object $entity
     * @return object
     * @throws \Exception
     */
    public function edit($entity)
    {
        try {
            $this->entityManager->flush();

            $this->logger->info(sprintf('Updated %s ID::%s', get_class($entity), $entity->getId()));

            return $entity;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error updating %s. Error: %s', get_class($entity), $e->getMessage()));

            throw $e;
        }
    }

    /**
     * @param object $entity
     *
     * @throws \Exception
     */
    public function remove($entity): void
    {
        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();

            $this->logger->info(sprintf('Removed %s ID::%s', get_class($entity), $entity->getId()));
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error removing %s. Error: %s', get_class($entity), $e->getMessage()));

            throw $e;
        }
    }

    /**
     * @param User $user
     * @param int $id
     * @return object|null
     */
    public function get(User $user, int $id): ?object
    {
        return $this->getRepository()->findOneBy(['id' => $id, 'user' => $user]);
    }

    /**
     * @param string|null $filter
     * @param int|null $page
     * @param int|null $limit
     * @param string $sort
     * @param string $dir
     *
     * @return array
     */
    public function getAll(
        string $filter = null,
        int $page = null,
        int $limit = null,
        string $sort = '',
        string $dir = ''
    ): array {
        $orderBy = ['id' => 'DESC'];
        if ($sort && in_array($sort, $this->getSortFields())) {
            $orderBy = [(string) $sort => $dir ? strtoupper($dir) : 'ASC'];
        }

        $offset = $page !== null && $limit !== null ? ($page - 1) * $limit : null;

        $entities = $this->getRepository()->getAll($filter, $orderBy, $limit, $offset);
        $total = $this->getRepository()->getAllCount($filter);

        return ['total' => $total, 'data' => $entities];
    }

    /**
     * @return int
     */
    public function getAllCount(): int
    {
        return $this->getRepository()->getAllCount(null);
    }

    /**
     * @param object $entity
     * @param array $data
     * @return object
     * @throws \ReflectionException
     */
    public function process(object $entity, array $data): object
    {
        $reflectionClass = new \ReflectionClass($entity);

        foreach ($data as $property => $value) {
            if (!$reflectionClass->hasProperty($property)) {
                continue;
            }

            $reflectionProperty = new \ReflectionProperty($entity, $property);
            $matches = null;
            if (preg_match('/@var\s+([^\s]+)/', $reflectionProperty->getDocComment(), $matches)) {
                list(, $type) = $matches;
                $type = str_replace(['\\', '|', 'null'], '', $type);

                if (strpos($type, 'DateTime') !== false) {
                    $value = new \DateTime($value);
                }

                $entityClass= sprintf('App\\Entity\\%s', $type);
                if (in_array($entityClass, self::ENTITIES)) {
                    $repository = $this->entityManager->getRepository($entityClass);
                    if (!$repository instanceof ObjectRepository) {
                        throw new \Exception('Repository not found for that entity');
                    }
                    $value = $repository->find($value);
                }
            }

            $reflectionMethod = $reflectionClass->getMethod(sprintf('set%s', ucwords($property)));
            if ($reflectionMethod instanceof \ReflectionMethod) {
                $function = $reflectionMethod->getName();
                $entity->$function($value);
            }
        }

        return $entity;
    }
}
