<?php

namespace App\Controller;

use App\Library\Controller\BaseController;
use App\Service\TaskService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/semanal", name="task_")
 */
class TaskController extends BaseController
{
    private $taskService;
    private $serializer;

    public function __construct(TaskService $taskService, SerializerInterface $serializer)
    {
        $this->taskService = $taskService;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="index")
     *
     * @return Response
     */
    public function index(): Response
    {
        $tasks = $this->taskService->getFromCurrent($this->getUserInstance());
        return $this->render('task/index.html.twig', ['tasks' => $tasks]);
    }

    /**
     * @Route("/add", name="post")
     *
     * @param Request $request
     * @return Response
     */
    public function post(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $data = json_decode($request->getContent(), true);

        try {
            $task = $this->taskService->post($this->getUserInstance(), $data['title'], $data['date']);
            $task = $this->serializer->serialize($task, 'json', ['groups' => 'show']);

            return new Response($task, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
