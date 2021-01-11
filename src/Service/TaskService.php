<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\TaskTag;
use App\Entity\User;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var LoggerInterface */
    private $logger;

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
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->validator = $validator;
        $this->tagService = $tagService;
        $this->repository = $this->entityManager->getRepository(Task::class);
    }

    /**
     * @param int $id
     * @return Task|null
     */
    public function get(int $id): ?Task
    {
        return $this->repository->find($id);
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
     * @param Task $task
     * @return Task
     * @throws \Exception
     */
    public function create(Task $task): Task
    {
        try {
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $this->logger->info(sprintf('Task created! ID %d', $task->getId()));

            return $task;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error when creating task: %s', $e->getMessage()));
            throw $e;
        }
    }

    /**
     * @param User $user
     * @param string $title
     * @param string $date
     * @return Task
     * @throws \Exception
     */
    public function post(User $user, string $title, string $date): Task
    {
        $task = new Task();
        $task->setTitle($title);
        $task->setDate(new \DateTime($date));
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
}
