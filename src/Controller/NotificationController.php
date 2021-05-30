<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Library\Controller\BaseController;
use App\Service\NotificationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/notificaciones", name="notification_")
 * @IsGranted("ROLE_USER")
 */
class NotificationController extends BaseController
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * @Route("/", name="index")
     * @return Response
     */
    public function index(Request $request): Response
    {

        $user = $this->getUserInstance();
        $notifications = $this->notificationService->getAll($user, null, null, null, 'createdAt', 'DESC');

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications['data'],
        ]);
    }

    /**
     * @Route("/{id}", name="handle")
     *
     * @param int $id
     * @return Response
     * @throws \Exception
     */
    public function handle(int $id): Response
    {
        $user = $this->getUserInstance();
        $notification = $this->notificationService->get($user, $id);
        if (!$notification instanceof Notification) {
            throw new NotFoundHttpException();
        }

        $notification->setStatus(Notification::READ_STATUS);
        $this->notificationService->edit($notification);

        return $this->redirectToRoute($this->notificationService->getRoute($notification->getType()));
    }
}
