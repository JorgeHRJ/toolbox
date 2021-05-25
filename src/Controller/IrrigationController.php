<?php

namespace App\Controller;

use App\Entity\IrrigationData;
use App\Entity\IrrigationZone;
use App\Library\Controller\BaseController;
use App\Service\IrrigationDataService;
use App\Service\IrrigationZoneService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/recomendaciones-riego", name="irrigation_")
 * @IsGranted("ROLE_IRRIGATION")
 */
class IrrigationController extends BaseController
{
    private IrrigationDataService $dataService;
    private IrrigationZoneService $zoneService;

    public function __construct(IrrigationDataService $dataService, IrrigationZoneService $zoneService)
    {
        $this->dataService = $dataService;
        $this->zoneService = $zoneService;
    }

    /**
     * @Route("/", name="zones")
     * @return Response
     */
    public function zones(): Response
    {
        return $this->render('irrigation/zones.html.twig', [
            'zones' => $this->zoneService->getPartialZones()
        ]);
    }

    /**
     * @Route("/zona/{zoneId}", name="zone", requirements={"zoneId"="\d+"})
     * @param int $zoneId
     * @return Response
     */
    public function zone(int $zoneId): Response
    {
        $zone = $this->zoneService->getDataById($zoneId);
        if (!$zone instanceof IrrigationZone) {
            throw new NotFoundHttpException();
        }

        return $this->render('irrigation/zone.html.twig', [
            'zone' => $zone,
            'zone_data' => $this->dataService->prepareZoneData($zone)
        ]);
    }

    /**
     * @Route("/zona/descargar/{dataId}", name="download", requirements={"dataId"="\d+"})
     *
     * @param int $dataId
     * @return Response
     */
    public function download(int $dataId): Response
    {
        $data = $this->dataService->get(null, $dataId);
        if (!$data instanceof IrrigationData) {
            throw new NotFoundHttpException();
        }

        $path = $this->dataService->getFilenamePath($data);

        $response = new BinaryFileResponse($path);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $data->getFilename());

        return $response;
    }
}
