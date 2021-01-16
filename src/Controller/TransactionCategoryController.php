<?php

namespace App\Controller;

use App\Entity\TransactionCategory;
use App\Form\TransactionCategoryType;
use App\Library\Controller\BaseController;
use App\Service\TransactionCategoryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/monedero", name="transactioncategory_")
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

    /**
     * @Route("/nuevo", name="new")
     *
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $category = new TransactionCategory();

        $form = $this->createForm(TransactionCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash('app_error', $this->getFormErrorMessagesList($form, true));
                return $this->render('transactioncategory/new.html.twig', ['form' => $form->createView()]);
            }

            try {
                $this->transactionCategoryService->new(
                    $this->getUserInstance(),
                    $category,
                    $form->get('month')->getData(),
                    $form->get('date')->getData()
                );
                $this->addFlash('app_success', '¡Categoría creada con éxito!');

                return $this->redirectToRoute('transactioncategory_index');
            } catch (\Exception $e) {
                $this->addFlash('app_error', 'Hubo un problema a la hora de crear la categoría');
            }
        }

        return $this->render('transactioncategory/new.html.twig', ['form' => $form->createView()]);
    }
}
