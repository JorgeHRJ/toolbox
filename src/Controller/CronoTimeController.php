<?php

namespace App\Controller;

use App\Entity\CronoClient;
use App\Entity\CronoMonth;
use App\Entity\CronoTime;
use App\Form\CronoTimeType;
use App\Library\Controller\BaseController;
use App\Service\CronoClientService;
use App\Service\CronoMonthService;
use App\Service\CronoTimeService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/cronos", name="crono_time_")
 * @IsGranted("ROLE_CRONOS")
 */
class CronoTimeController extends BaseController
{
    private CronoTimeService $timeService;
    private CronoMonthService $monthService;
    private CronoClientService $clientService;
    private SerializerInterface $serializer;

    public function __construct(
        CronoTimeService $timeService,
        CronoMonthService $monthService,
        CronoClientService $clientService,
        SerializerInterface $serializer
    ) {
        $this->timeService = $timeService;
        $this->monthService = $monthService;
        $this->clientService = $clientService;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="index")
     * @return Response
     */
    public function index(): Response
    {
        $user = $this->getUserInstance();
        $times = $this->timeService->getFromCurrent($user, new \DateTime());

        return $this->render('crono/index.html.twig', [
            'times' => $times
        ]);
    }

    /**
     * @Route("/form/{timeId}", methods={"GET", "POST", "PATCH"}, name="form")
     * @param Request $request
     * @param int|null $timeId
     * @return Response
     */
    public function form(Request $request, int $timeId = null): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(['message' => 'Bad Request'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUserInstance();
        $time = $timeId !== null ? $this->timeService->get(null, $timeId) : new CronoTime();
        if (!$time instanceof CronoTime) {
            return new JsonResponse(['message' => 'Time not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(CronoTimeType::class, $time, ['user' => $user]);
        if ($request->getMethod() === Request::METHOD_GET) {
            return new JsonResponse([
                'html' => $this->renderView(
                    'crono/components/time-modal-form.html.twig',
                    ['form' => $form->createView()]
                ),
                'url' => $this->generateUrl('crono_time_form', ['timeId' => $timeId]),
                'remove_url' => $timeId !== null
                    ? $this->generateUrl('crono_time_remove', ['timeId' => $timeId])
                    : null,
                'method' => $timeId === null ? Request::METHOD_POST : Request::METHOD_PATCH
            ]);
        }

        $data = json_decode($request->getContent(), true);
        $client = $this->clientService->get($user, $data['client']);
        if (!$client instanceof CronoClient) {
            return new JsonResponse(['message' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }
        unset($data['client']);

        $form->submit($data);
        $time->setClient($client);
        if (!$time->getStartAt() instanceof \DateTime) {
            return new JsonResponse(['message' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $month = $this->monthService->getFromDate($user, $time->getStartAt());
        if (!$month instanceof CronoMonth) {
            $month = $this->monthService->new(
                $user,
                (int) $time->getStartAt()->format('Y'),
                (int) $time->getStartAt()->format('m')
            );
        }
        $time->setMonth($month);

        $time = $request->getMethod() === Request::METHOD_POST
            ? $this->timeService->create($time)
            : $this->timeService->edit($time);

        return new Response($this->serializer->serialize($time, 'json', ['groups' => 'detail']));
    }

    /**
     * @Route("/remove/{timeId}", methods={"DELETE"}, name="remove")
     * @param int $timeId
     * @return Response
     */
    public function remove(Request $request, int $timeId): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(['message' => 'Bad Request'], Response::HTTP_BAD_REQUEST);
        }

        $time = $this->timeService->get(null, $timeId);
        if (!$time instanceof CronoTime) {
            return new JsonResponse(['message' => 'Time not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->timeService->remove($time);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['message' => 'Error when trying to remove the time'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(['id' => $timeId], Response::HTTP_OK);
    }
}
