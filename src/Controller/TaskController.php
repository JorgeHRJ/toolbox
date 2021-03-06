<?php

namespace App\Controller;

use App\Entity\Task;
use App\Library\Controller\BaseController;
use App\Service\TaskService;
use App\Service\TaskTagService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/semanal", name="task_")
 * @IsGranted("ROLE_TASK")
 */
class TaskController extends BaseController
{
    private $taskService;
    private $tagService;
    private $serializer;

    public function __construct(TaskService $taskService, TaskTagService $tagService, SerializerInterface $serializer)
    {
        $this->taskService = $taskService;
        $this->tagService = $tagService;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="index")
     *
     * @return Response
     */
    public function index(): Response
    {
        $user = $this->getUserInstance();

        $tasks = $this->taskService->getFromCurrent($user);
        $tags = $this->tagService->getByUser($user);

        return $this->render('task/index.html.twig', ['tasks' => $tasks, 'tags' => $tags]);
    }

    /**
     * @Route("/add", name="post", methods={"POST"})
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
            $task = $this->taskService->post($this->getUserInstance(), $data);
            $task = $this->serializer->serialize($task, 'json', ['groups' => 'show']);

            return new Response($task, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/edit/{id}", name="patch", methods={"PATCH"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function patch(Request $request, int $id): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new Response(null, Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUserInstance();
        $task = $this->taskService->get($user, $id);
        if (!$task instanceof Task) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        try {
            $task = $this->taskService->patch($task, $data);
            $task = $this->serializer->serialize($task, 'json', ['groups' => 'show']);

            return new Response($task, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/delete/{id}", name="delete", requirements={"id"="\d+"}, methods={"DELETE"})
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, int $id): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new Response(null, Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUserInstance();
        $task = $this->taskService->get($user, $id);
        if (!$task instanceof Task) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        try {
            $taskId = $task->getId();
            $this->taskService->remove($task);

            return new JsonResponse(['id' => $taskId], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
