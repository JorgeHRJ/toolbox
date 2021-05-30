<?php

namespace App\Twig\Extension;

use App\Entity\Notification;
use App\Entity\User;
use App\Library\Event\IrrigationNotificationEvent;
use App\Library\Event\ReservoirNotificationEvent;
use App\Service\NotificationService;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationExtension extends AbstractExtension
{
    private NotificationService $notificationService;
    private Security $security;

    public function __construct(NotificationService $notificationService, Security $security)
    {
        $this->notificationService = $notificationService;
        $this->security = $security;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_user_notifications', [$this, 'getUserNotifications']),
            new TwigFunction('get_unread_notifications_total', [$this, 'getUnreadNotificationsTotal']),
            new TwigFunction('get_notification_type_info', [$this, 'getNotificationTypeInfo'])
        ];
    }

    /**
     * @return Notification[]
     */
    public function getUserNotifications(): array
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        $result = $this->notificationService->getAll($user, null, null, 3, 'createdAt', 'DESC');
        return $result['data'];
    }

    public function getUnreadNotificationsTotal(): int
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        return $this->notificationService->countUnread($user);
    }

    public function getNotificationTypeInfo(string $type): array
    {
        $types = [
            IrrigationNotificationEvent::EVENT => ['label' => 'Recomendaciones de Riego', 'badge' => 'warning'],
            ReservoirNotificationEvent::EVENT => ['label' => 'Balsas', 'badge' => 'info'],
        ];

        return $types[$type] ?? [];
    }
}
