<?php

namespace App\Controller;

use App\Entity\CyclistRace;
use App\Entity\Race;
use App\Form\CyclistRaceType;
use App\Library\Controller\BaseController;
use App\Service\CyclistRaceService;
use App\Service\RaceService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/libro-ruta", name="racebook_")
 * @IsGranted("ROLE_RACEBOOK")
 */
class RaceBookController extends BaseController
{
    const LIST_LIMIT = 10;

    private RaceService $raceService;
    private CyclistRaceService $cyclistRaceService;

    public function __construct(RaceService $raceService, CyclistRaceService $cyclistRaceService)
    {
        $this->raceService = $raceService;
        $this->cyclistRaceService = $cyclistRaceService;
    }

    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        list($page, $limit, $sort, $dir, $filters) = $this->handleIndexRequest($request, self::LIST_LIMIT);

        $races = $this->raceService->getAll(null, $filters, $page, $limit, $sort, $dir);
        $paginationData = $this->getPaginationData($request, $races, $page, $limit);

        return $this->render('racebook/index.html.twig', array_merge(
            $races,
            [
                'sort' => $request->query->get('sort'),
                'dir' => $request->query->get('dir'),
                'paginationData' => $paginationData,
                'params' => $request->query->all()
            ]
        ));
    }

    /**
     * @Route("/{raceSlug}", name="race")
     *
     * @param string $raceSlug
     * @return Response
     */
    public function race(string $raceSlug): Response
    {
        $user = $this->getUserInstance();
        $race = $this->raceService->getBySlug($raceSlug);
        if (!$race instanceof Race) {
            throw new NotFoundHttpException();
        }

        $cyclistsRaces = $this->cyclistRaceService->getByUserAndRace($user, $race);
        $teamsData = $this->cyclistRaceService->makeTeamCentered($cyclistsRaces);

        return $this->render('racebook/race.html.twig', [
            'race' => $race,
            'teams' => $teamsData
        ]);
    }

    /**
     * @Route("/{raceSlug}/{cyclistSlug}", name="cyclist_race")
     *
     * @param Request $request
     * @param string $raceSlug
     * @param string $cyclistSlug
     * @return Response
     */
    public function cyclistRace(Request $request, string $raceSlug, string $cyclistSlug): Response
    {
        $user = $this->getUserInstance();
        $cyclistRace = $this->cyclistRaceService->getByUserRaceSlugAndCyclistSlug($user, $raceSlug, $cyclistSlug);
        if (!$cyclistRace instanceof CyclistRace) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(CyclistRaceType::class, $cyclistRace);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash('app_error', $this->getFormErrorMessagesList($form, true));
                return $this->render('racebook/cyclist_race.html.twig', [
                    'form' => $form->createView(),
                    'cyclist_race' => $cyclistRace
                ]);
            }

            try {
                $this->cyclistRaceService->edit($cyclistRace);
            } catch (\Exception $exception) {
                $this->addFlash('app_error', 'Hubo un problema a la hora de editar la información del ciclista');
            }
        }

        return $this->render('racebook/cyclist_race.html.twig', [
            'form' => $form->createView(),
            'cyclist_race' => $cyclistRace
        ]);
    }

    /**
     * @Route("/{raceSlug}/cyclist/suggest", name="cyclist_race_suggest")
     *
     * @param Request $request
     * @return Response
     */
    public function cyclistSuggest(Request $request, string $raceSlug): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(['message' => 'Bad request'], Response::HTTP_BAD_REQUEST);
        }

        $race = $this->raceService->getBySlug($raceSlug);
        if (!$race instanceof Race) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $query = $request->get('q');
        if ($query === null) {
            return new JsonResponse(['message' => 'Query parameter nedded!'], Response::HTTP_BAD_REQUEST);
        }

        $suggestions = $this->cyclistRaceService->suggest($race, $query);

        return new JsonResponse(['suggestions' => $suggestions], Response::HTTP_OK);
    }
}
