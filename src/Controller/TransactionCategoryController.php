<?php

namespace App\Controller;

use App\Entity\TransactionCategory;
use App\Library\Controller\BaseController;
use App\Service\TransactionCategoryService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/monedero", name="transaction_")
 */
class TransactionCategoryController extends BaseController
{
    const LIST_LIMIT = 10;

    private $transactionCategoryService;

    public function __construct(TransactionCategoryService $transactionCategoryService)
    {
        $this->transactionCategoryService = $transactionCategoryService;
    }

    /**
     * @Route("/{year}{month}", name="index", requirements={"year"="\d{4}", "month"="\d{2}"})
     *
     * @param string|null $year
     * @param string|null $month
     * @return Response
     */
    public function index(string $year = null, string $month = null): Response
    {
        $user = $this->getUserInstance();

        if ($year === null && $month === null) {
            $now = new \DateTime();
            $year = $now->format('Y');
            $month = $now->format('m');
        }

        $incomes = $this->transactionCategoryService->getByTypeMonthAndYear(
            $user,
            TransactionCategory::INCOME_TYPE,
            $year,
            $month
        );
        $expenses = $this->transactionCategoryService->getByTypeMonthAndYear(
            $user,
            TransactionCategory::EXPENSE_TYPE,
            $year,
            $month
        );

        return $this->render('transactioncategory/index.html.twig', [
            'incomes' => $incomes,
            'expenses' => $expenses
        ]);
    }
}
