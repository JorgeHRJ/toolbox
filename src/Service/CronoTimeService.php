<?php

namespace App\Service;

use App\Entity\CronoTime;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\CronoTimeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class CronoTimeService extends BaseService
{
    private CronoTimeRepository $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(CronoTime::class);
    }

    public function getFromCurrent(User $user, \DateTime $current): array
    {
        $startMonth = clone $current;
        $endMonth = clone $current;

        $startMonth->modify('first day of this month');
        $endMonth->modify('last day of this month');

        $startMonth->modify('-3 months');
        $endMonth->modify('+1 month');

        return $this->repository->getBetweenDates(
            $user,
            $startMonth->format('Y-m-d'),
            $endMonth->format('Y-m-d')
        );
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
