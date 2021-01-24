<?php

namespace App\Controller;

use App\Entity\Reservoir;
use App\Entity\ReservoirProcess;
use App\Library\Controller\BaseController;
use App\Service\ReservoirDataService;
use App\Service\ReservoirProcessService;
use App\Service\ReservoirService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/balsas", name="reservoir_")
 * @IsGranted("ROLE_RESERVOIR")
 */
class ReservoirController extends BaseController
{
    private $reservoirService;
    private $dataService;
    private $processService;

    public function __construct(
        ReservoirService $reservoirService,
        ReservoirDataService $dataService,
        ReservoirProcessService $processService
    ) {
        $this->reservoirService = $reservoirService;
        $this->dataService = $dataService;
        $this->processService = $processService;
    }

    /**
     * @Route("/", name="index")
     *
     * @return Response
     */
    public function index(): Response
    {
        $data = $this->dataService->getData();

        return $this->render('reservoir/index.html.twig', ['data' => $data]);
    }

    /**
     * @Route("/balsa/{id}", name="detail", requirements={"id"="\d+"})
     *
     * @param int $id
     * @return Response
     */
    public function detail(int $id): Response
    {
        $reservoir = $this->reservoirService->getWithData($id);
        if (!$reservoir instanceof Reservoir) {
            throw new NotFoundHttpException();
        }

        return $this->render('reservoir/detail.html.twig', ['reservoir' => $reservoir]);
    }

    /**
     * @Route("/balsa/descargar/{processId}", name="download", requirements={"processId"="\d+"})
     *
     * @param int $processId
     * @return Response
     */
    public function download(int $processId): Response
    {
        $process = $this->processService->get(null, $processId);
        if (!$process instanceof ReservoirProcess) {
            throw new NotFoundHttpException();
        }

        $path = $this->processService->getFilenamePath($process);

        $response = new BinaryFileResponse($path);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $process->getFilename());

        return $response;
    }
}
