<?php

namespace App\Service;

use App\Entity\TransactionCategory;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\TransactionCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class TransactionCategoryService extends BaseService
{
    /** @var TransactionCategoryRepository */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(TransactionCategory::class);
    }

    /**
     * @param User $user
     * @param int $type
     * @param string $year
     * @param string $month
     * @return TransactionCategory[]|array
     */
    public function getByTypeMonthAndYear(User $user, int $type, string $year, string $month): array
    {
        return $this->repository->findByTypeMonthAndYear($user, $type, $year, $month);
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
