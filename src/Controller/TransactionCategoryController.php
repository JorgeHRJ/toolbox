<?php

namespace App\Controller;

use App\Entity\TransactionCategory;
use App\Entity\TransactionMonth;
use App\Form\TransactionCategoryType;
use App\Library\Controller\BaseController;
use App\Service\TransactionCategoryService;
use App\Service\TransactionMonthService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/monedero", name="transactioncategory_")
 */
class TransactionCategoryController extends BaseController
{
    const LIST_LIMIT = 10;

    private $categoryService;
    private $monthService;

    public function __construct(
        TransactionCategoryService $categoryService,
        TransactionMonthService $monthService
    ) {
        $this->categoryService = $categoryService;
        $this->monthService = $monthService;
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

        $incomes = $this->categoryService->getByTypeMonthAndYear(
            $user,
            TransactionCategory::INCOME_TYPE,
            $year,
            $month
        );
        $expenses = $this->categoryService->getByTypeMonthAndYear(
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
                $this->categoryService->new(
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

    /**
     * @Route("/editar/{monthId}", name="edit", requirements={"monthId"="\d+"})
     *
     * @param Request $request
     * @param int $monthId
     * @return Response
     */
    public function edit(Request $request, int $monthId): Response
    {
        $user = $this->getUserInstance();
        $month = $this->monthService->getById($monthId);
        if (!$month instanceof TransactionMonth) {
            throw new NotFoundHttpException();
        }

        $category = $month->getCategory();
        if ($category->getUser()->getId() !== $user->getId()) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(TransactionCategoryType::class, $category);
        $form->get('month')->get('expected')->setData($month->getExpected());

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash('app_error', $this->getFormErrorMessagesList($form, true));
                return $this->render('transactioncategory/edit.html.twig', ['form' => $form->createView()]);
            }

            try {
                /** @var TransactionMonth $monthForm */
                $monthForm = $form->get('month')->getData();
                $month->setExpected($monthForm->getExpected());

                $this->categoryService->edit($category);
                $this->monthService->edit($month);

                $this->addFlash('app_success', '¡Categoría editada con éxito!');

                return $this->redirectToRoute('transactioncategory_index');
            } catch (\Exception $e) {
                $this->addFlash('app_error', 'Hubo un problema a la hora de editada la categoría');
            }
        }

        return $this->render('transactioncategory/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/eliminar/{id}", name="delete", requirements={"id"="\d+"})
     *
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $user = $this->getUserInstance();
        $category = $this->categoryService->get($user, $id);
        if (!$category instanceof TransactionCategory) {
            throw new NotFoundHttpException();
        }

        try {
            $this->categoryService->remove($category);

            $this->addFlash('app_success', '¡Categoría eliminada con éxito!');
        } catch (\Exception $e) {
            $this->addFlash('app_error', 'Hubo un error a la hora de eliminar la categoría');
        }

        return $this->redirectToRoute('transactioncategory_index');
    }
}
