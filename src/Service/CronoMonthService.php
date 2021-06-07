<?php

namespace App\Service;

use App\Entity\CronoMonth;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\CronoMonthRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class CronoMonthService extends BaseService
{
    private CronoMonthRepository $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(CronoMonth::class);
    }

    public function new(User $user, int $year, int $month): CronoMonth
    {
        $cronoMonth = new CronoMonth();
        $cronoMonth
            ->setUser($user)
            ->setYear($year)
            ->setMonth($month);

        $this->entityManager->persist($cronoMonth);
        $this->entityManager->flush();

        return $cronoMonth;
    }

    public function getFromDate(User $user, \DateTime $date): ?CronoMonth
    {
        return $this->repository->findOneBy([
            'month' => (int) $date->format('m'),
            'year' => (int) $date->format('Y'),
            'user' => $user
        ]);
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
