<?php

namespace App\Service;

use App\Entity\TransactionMonth;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\TransactionMonthRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class TransactionMonthService extends BaseService
{
    /** @var TransactionMonthRepository */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(TransactionMonth::class);
    }

    /**
     * @param int $id
     * @return TransactionMonth|null
     */
    public function getById(int $id): ?TransactionMonth
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
