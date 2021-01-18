<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class TransactionService extends BaseService
{
    /** @var TransactionRepository */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(Transaction::class);
    }

    /**
     * @param int $id
     * @return Transaction|null
     */
    public function getById(int $id): ?Transaction
    {
        return $this->repository->find($id);
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
