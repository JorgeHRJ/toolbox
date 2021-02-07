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
        return $this->repository->findByTypeMonthAndYear($user, $type, $year, $month);
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

    public function getBalance(User $user, string $year, string $month): array
    {
        $data = [];

        $date = new \DateTime(sprintf('%s/%s/01', $year, $month));
        $data[sprintf('%s %s', $this->getSpanishMonth($month), $year)]
            = $this->getBalanceDataFromMonth($user, $date);

        for ($i = 1; $i < 6; $i++) {
            $date = $date->modify('-1 month');

            $month = $date->format('m');
            $year = $date->format('Y');

            $data[sprintf('%s %s', $this->getSpanishMonth($month), $year)]
                = $this->getBalanceDataFromMonth($user, $date);
        }

        return array_reverse($data);
    }

    private function getBalanceDataFromMonth(User $user, \DateTimeInterface $date): array
    {
        $year = $date->format('Y');
        $month = $date->format('m');

        $incomes = $this->getByTypeMonthAndYear(
            $user,
            TransactionCategory::INCOME_TYPE,
            $year,
            $month
        );
        $totalIncomes = 0;
        foreach ($incomes as $income) {
            $totalIncomes += $income->getMonths()[0]->getValue();
        }

        $expenses = $this->getByTypeMonthAndYear(
            $user,
            TransactionCategory::EXPENSE_TYPE,
            $year,
            $month
        );
        $totalExpenses = 0;
        foreach ($expenses as $expense) {
            $totalExpenses += $expense->getMonths()[0]->getValue();
        }

        return ['total_incomes' => $totalIncomes, 'total_expenses' => $totalExpenses];
    }

    private function getSpanishMonth(string $month): string
    {
        $months = [
            '01' => 'enero', '02' => 'febrero', '03' => 'marzo', '04' => 'abril', '05' => 'mayo',
            '06' => 'junio', '07' => 'julio', '08' => 'agosto', '09' => 'septiembre', '10' => 'octubre',
            '11' => 'noviembre', '12' => 'diciembre'
        ];

        return $months[$month];
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
