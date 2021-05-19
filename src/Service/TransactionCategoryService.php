<?php

namespace App\Service;

use App\Entity\TransactionCategory;
use App\Entity\TransactionMonth;
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
     * @param TransactionCategory $transactionCategory
     * @param TransactionMonth $transactionMonth
     * @param string $date
     * @return TransactionCategory
     * @throws \Exception
     */
    public function new(
        User $user,
        TransactionCategory $transactionCategory,
        TransactionMonth $transactionMonth,
        string $date
    ): TransactionCategory {
        list($month, $year) = explode('/', $date);

        $transactionMonth->setValue((string) 0);
        $transactionMonth->setYear((int) $year);
        $transactionMonth->setMonth((int) $month);
        $transactionMonth->setCategory($transactionCategory);
        $transactionMonth->setUser($user);

        $transactionCategory->addMonth($transactionMonth);

        $transactionCategory->setUser($user);

        return $this->create($transactionCategory);
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
        return $this->repository->findByTypeMonthAndYear($user, $type, (int) $year, (int) $month);
    }

    /**
     * @param string $year
     * @param string $month
     * @return TransactionCategory[]|array
     */
    public function getMonthlyFromPreviousMonth(string $year, string $month): array
    {
        return $this->repository->findMonthlyFromPreviousMonth($year, $month);
    }

    public function getBalance(User $user, int $year, int $month): array
    {
        $dates = [];
        $dates[] = ['year' => $year, 'month' => $month];

        $date = new \DateTime(sprintf('%s/%s/01', $year, $month));
        for ($i = 1; $i < 6; $i++) {
            $date = $date->modify('-1 month');

            $month = $date->format('m');
            $year = $date->format('Y');

            $dates[] = ['year' => $year, 'month' => $month];
        }

        return $this->getBalanceDataFromMonths($user, $dates);
    }

    /**
     * @param User $user
     * @param array $dates
     * @return array
     */
    private function getBalanceDataFromMonths(User $user, array $dates): array
    {
        $data = [];
        foreach ($dates as $date) {
            $incomes = $this->repository->getTypeTotalValuesFromMonthYear(
                $user,
                TransactionCategory::INCOME_TYPE,
                $date['year'],
                $date['month']
            );
            $expenses = $this->repository->getTypeTotalValuesFromMonthYear(
                $user,
                TransactionCategory::EXPENSE_TYPE,
                $date['year'],
                $date['month']
            );

            $data[sprintf('%s %s', $this->getSpanishMonth($date['month']), $date['year'])]
                = ['total_incomes' => $incomes, 'total_expenses' => $expenses];
        }

        return array_reverse($data);
    }

    private function getSpanishMonth(int $month): string
    {
        $months = [
            'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre',
            'noviembre', 'diciembre'
        ];

        return $months[$month - 1];
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
