<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use App\Library\Event\IrrigationNotificationEvent;
use App\Library\Event\ReservoirNotificationEvent;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;

class NotificationService extends BaseService
{
    private NotificationRepository $repository;
    private RouterInterface $router;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        RouterInterface $router
    ) {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(Notification::class);
        $this->router = $router;
    }

    public function countUnread(User $user): int
    {
        return $this->repository->countUnread($user);
    }

    public function getRoute(string $type): string
    {
        $routes = [
            IrrigationNotificationEvent::EVENT => 'irrigation_zones',
            ReservoirNotificationEvent::EVENT => 'reservoir_index'
        ];

        return $routes[$type];
    }

    public function getSortFields(): array
    {
        return [];
    }

    public function getRepository(): BaseRepository
    {
        return $this->repository;
    }
}
