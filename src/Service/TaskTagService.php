<?php

namespace App\Service;

use App\Entity\TaskTag;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\TaskTagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskTagService extends BaseService
{
    /** @var ValidatorInterface */
    private $validator;

    /** @var TaskTagRepository */
    private $repository;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        ValidatorInterface $validator
    ) {
        parent::__construct($entityManager, $logger);
        $this->validator = $validator;
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

    public function post(User $user, array $data): TaskTag
    {
        $tag = new TaskTag();
        $tag->setName($data['name']);
        $tag->setColor($data['color']);
        $tag->setUser($user);

        $errors = $this->validator->validate($tag);
        if (count($errors) > 0) {
            $errorMessage = 'La información de la etiqueta a crear no es válida';
            if ($errors instanceof ConstraintViolationList) {
                $errorMessage = $errors->__toString();
            }

            throw new \Exception($errorMessage);
        }

        try {
            return $this->create($tag);
        } catch (\Exception $e) {
            throw new \Exception('Hubo un error al crear la etiqueta');
        }
    }

    public function getSortFields(): array
    {
        return [];
    }

    /**
     * @return TaskTagRepository
     */
    public function getRepository(): BaseRepository
    {
        return $this->repository;
    }
}
