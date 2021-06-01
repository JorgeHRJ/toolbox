<?php

namespace App\Controller;

use App\Entity\CronoClient;
use App\Form\CronoClientType;
use App\Library\Controller\BaseController;
use App\Service\CronoClientService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cronos/clientes", name="crono_client_")
 * @IsGranted("ROLE_CRONOS")
 */
class CronoClientController extends BaseController
{
    const LIST_LIMIT = 2;

    private CronoClientService $clientService;

    public function __construct(CronoClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        list($page, $limit, $sort, $dir, $filter) = $this->handleIndexRequest($request, self::LIST_LIMIT);
        $user = $this->getUserInstance();

        $clients = $this->clientService->getAll($user, $filter, $page, $limit, $sort, $dir);
        $paginationData = $this->getPaginationData($request, $clients, $page, $limit);

        return $this->render('crono/client/index.html.twig', array_merge(
            $clients,
            [
                'sort' => $request->query->get('sort'),
                'dir' => $request->query->get('dir'),
                'paginationData' => $paginationData,
                'params' => $request->query->all()
            ]
        ));
    }

    /**
     * @Route("/nuevo", methods="GET|POST", name="new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $client = new CronoClient();

        $form = $this->createForm(CronoClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash('app_error', $this->getFormErrorMessagesList($form, true));
                return $this->render('crono/client/new.html.twig', [
                    'form' => $form->createView()
                ]);
            }

            try {
                $user = $this->getUserInstance();
                $client->setUser($user);

                $this->clientService->create($client);

                $this->addFlash('app_success', '¡Cliente creado con éxito!');

                return $this->redirectToRoute('crono_client_index');
            } catch (\Exception $e) {
                $this->addFlash('app_error', 'Hubo un problema a la hora de crear el cliente');
            }
        }

        return $this->render('crono/client/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/editar/{id}", methods="GET|POST", name="edit")
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function edit(Request $request, int $id): Response
    {
        $user = $this->getUserInstance();
        $client = $this->clientService->get($user, $id);
        if (!$client instanceof CronoClient) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(CronoClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash('app_error', $this->getFormErrorMessagesList($form, true));
                return $this->render('crono/client/edit.html.twig', [
                    'form' => $form->createView(),
                    'client' => $client
                ]);
            }

            try {
                $this->clientService->edit($client);

                $this->addFlash('app_success', '¡Cliente editado con éxito!');

                return $this->redirectToRoute('crono_client_index');
            } catch (\Exception $e) {
                $this->addFlash('app_error', 'Hubo un problema a la hora de editar el cliente');
            }
        }

        return $this->render('crono/client/edit.html.twig', [
            'form' => $form->createView(),
            'client' => $client
        ]);
    }

    /**
     * @Route("/eliminar/{id}", name="remove")
     *
     * @param int $id
     * @return Response
     */
    public function remove(int $id): Response
    {
        $user = $this->getUserInstance();
        $client = $this->clientService->get($user, $id);
        if (!$client instanceof CronoClient) {
            throw new NotFoundHttpException();
        }

        try {
            $this->clientService->remove($client);
            $this->addFlash('app_success', '¡Cliente eliminado!');
        } catch (\Exception $e) {
            $this->addFlash('app_error', 'Hubo un error a la hora de eliminar el cliente');
        }

        return $this->redirectToRoute('crono_client_index');
    }
}
