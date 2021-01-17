<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\TransactionMonth;
use App\Form\TransactionType;
use App\Library\Controller\BaseController;
use App\Service\TransactionMonthService;
use App\Service\TransactionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/monedero", name="transaction_")
 */
class TransactionController extends BaseController
{
    private $transactionService;
    private $monthService;

    public function __construct(
        TransactionService $transactionService,
        TransactionMonthService $monthService
    ) {
        $this->transactionService = $transactionService;
        $this->monthService = $monthService;
    }

    /**
     * @Route("/{monthId}/movimientos/nuevo", name="new", requirements={"monthId"="\d+"})
     *
     * @param Request $request
     * @param int $monthId
     * @return Response
     */
    public function new(Request $request, int $monthId): Response
    {
        $user = $this->getUserInstance();

        $month = $this->monthService->getById($monthId);
        if (!$month instanceof TransactionMonth) {
            throw new NotFoundHttpException();
        }

        $transaction = new Transaction();

        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash('app_error', $this->getFormErrorMessagesList($form, true));
                return $this->render('transaction/new.html.twig', [
                    'form' => $form->createView(),
                    'month' => $month
                ]);
            }

            try {
                $transaction->setMonth($month);
                $value = ((float) $month->getValue()) + ((float) $transaction->getAmount());
                $month->setValue((string) $value);

                $this->transactionService->create($transaction);
                $this->addFlash('app_success', '¡Movimiento creado con éxito!');

                return $this->redirectToRoute('transactioncategory_index');
            } catch (\Exception $e) {
                $this->addFlash('app_error', 'Hubo un problema a la hora de crear el movimiento');
            }
        }

        return $this->render('transaction/new.html.twig', [
            'form' => $form->createView(),
            'month' => $month
        ]);
    }
}
