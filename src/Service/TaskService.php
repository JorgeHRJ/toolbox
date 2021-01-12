<?php

namespace App\Service;

use _HumbugBoxce6e9e339315\Roave\BetterReflection\Reflection\Adapter\ReflectionClass;
use App\Entity\Task;
use App\Entity\TaskTag;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskService extends BaseService
{
    /** @var ValidatorInterface */
    private $validator;

    /** @var TaskTagService */
    private $tagService;

    /** @var TaskRepository */
    private $repository;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        TaskTagService $tagService,
        ValidatorInterface $validator
    ) {
        parent::__construct($entityManager, $logger);
        $this->validator = $validator;
        $this->tagService = $tagService;
        $this->repository = $this->entityManager->getRepository(Task::class);
    }

    /**
     * @param User $user
     * @return array
     * @return Task[]|array
     */
    public function getFromCurrent(User $user): array
    {
        return $this->getByMonth($user, new \DateTime());
    }

    /**
     * @param User $user
     * @param \DateTime $date
     * @return Task[]|array
     */
    public function getByMonth(User $user, \DateTime $date): array
    {
        $startMonth = clone $date;
        $endMonth = clone $date;

        $startMonth->modify('first day of this month');
        $endMonth->modify('last day of this month');

        return $this->repository->getBetweenDates(
            $user,
            $startMonth->format('Y-m-d'),
            $endMonth->format('Y-m-d')
        );
    }

    /**
     * @param User $user
     * @param array $data
     * @return Task
     * @throws \Exception
     */
    public function post(User $user, array $data): Task
    {
        $task = new Task();
        $task->setTitle($data['title']);
        $task->setDate(new \DateTime($data['date']));
        $task->setStatus(Task::PENDING_STATUS);
        $task->setUser($user);

        $taskTag = $this->tagService->getByName($user, 'Test');
        if (!$taskTag instanceof TaskTag) {
            $taskTag = new TaskTag();
            $taskTag->setName('Test');
            $taskTag->setColor('danger');
            $taskTag->setUser($user);
        }

        $task->setTag($taskTag);

        $errors = $this->validator->validate($task);
        if (count($errors) > 0) {
            $errorMessage = 'La información de la tarea a crear no es válida';
            if ($errors instanceof ConstraintViolationList) {
                $errorMessage = $errors->__toString();
            }

            throw new \Exception($errorMessage);
        }

        try {
            return $this->create($task);
        } catch (\Exception $e) {
            throw new \Exception('Hubo un error al crear la tarea');
        }
    }

    /**
     * @param Task $task
     * @param array $data
     * @return Task
     * @throws \Exception
     */
    public function patch(Task $task, array $data): Task
    {
        $reflectionClass = new \ReflectionClass($task);

        foreach ($data as $property => $value) {
            if (!$reflectionClass->hasProperty($property)) {
                continue;
            }

            $reflectionProperty = new \ReflectionProperty($task, $property);
            $matches = null;
            if (preg_match('/@var\s+([^\s]+)/', $reflectionProperty->getDocComment(), $matches)) {
                list(, $type) = $matches;
                $type = str_replace(['\\', '|', 'null'], '', $type);

                if (strpos($type, 'DateTime') !== false) {
                    $value = new \DateTime($value);
                }
            }

            $reflectionMethod = $reflectionClass->getMethod(sprintf('set%s', ucwords($property)));
            if ($reflectionMethod instanceof \ReflectionMethod) {
                $function = $reflectionMethod->getName();
                $task->$function($value);
            }
        }

        try {
            return $this->edit($task);
        } catch (\Exception $e) {
            throw new \Exception('Hubo un error al editar la tarea');
        }
    }

    /**
     * @return array
     */
    public function getSortFields(): array
    {
        return [];
    }

    /**
     * @return TaskRepository
     */
    public function getRepository(): BaseRepository
    {
        return $this->repository;
    }
}
