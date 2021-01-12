<?php

namespace App\Controller;

use App\Library\Controller\BaseController;
use App\Service\TaskTagService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/semanal/etiquetas", name="tasktag_")
 */
class TaskTagController extends BaseController
{
    private $tagService;
    private $serializer;

    public function __construct(TaskTagService $tagService, SerializerInterface $serializer)
    {
        $this->tagService = $tagService;
        $this->serializer = $serializer;
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
            $task = $this->tagService->post($this->getUserInstance(), $data);
            $task = $this->serializer->serialize($task, 'json', ['groups' => 'show']);

            return new Response($task, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
