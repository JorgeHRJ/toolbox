<?php

namespace App\EventSubscriber;

use App\Entity\Notification;
use App\Entity\User;
use App\Library\Event\IrrigationNotificationEvent;
use App\Library\Event\NewUserNotificationEvent;
use App\Library\Event\ReservoirNotificationEvent;
use App\Library\Mail\NewIrrigationMail;
use App\Library\Mail\NewReservoirDataMail;
use App\Library\Mail\NewUserMail;
use App\Service\MailerService;
use App\Service\NotificationService;
use App\Service\UserService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotificationEventSubscriber implements EventSubscriberInterface
{
    private NotificationService $notificationService;
    private UserService $userService;
    private MailerService $mailerService;

    public function __construct(
        NotificationService $notificationService,
        UserService $userService,
        MailerService $mailerService
    ) {
        $this->notificationService = $notificationService;
        $this->userService = $userService;
        $this->mailerService = $mailerService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            IrrigationNotificationEvent::EVENT => 'onIrrigationNotification',
            ReservoirNotificationEvent::EVENT => 'onReservoirNotification'
        ];
    }

    public function onIrrigationNotification(IrrigationNotificationEvent $event): void
    {
        $users = $this->userService->getByRoles([User::ROLE_IRRIGATION, User::ROLE_ADMIN]);
        foreach ($users as $user) {
            $notification = new Notification();
            $notification
                ->setType(IrrigationNotificationEvent::EVENT)
                ->setContent('Se ha procesado una nueva Recomendación de Riego')
                ->setUser($user)
                ->setStatus(Notification::UNREAD_STATUS);

            $this->notificationService->create($notification);

            if ($user->getReportable()) {
                $mail = new NewIrrigationMail();
                $mail->prepare($user->getEmail(), []);
                $this->mailerService->send($mail);
            }
        }
    }

    public function onReservoirNotification(ReservoirNotificationEvent $event): void
    {
        $users = $this->userService->getByRoles([User::ROLE_IRRIGATION, User::ROLE_ADMIN]);
        foreach ($users as $user) {
            $notification = new Notification();
            $notification
                ->setType(ReservoirNotificationEvent::EVENT)
                ->setContent('Se han procesado nuevos datos de Balsas')
                ->setUser($user)
                ->setStatus(Notification::UNREAD_STATUS);

            $this->notificationService->create($notification);

            if ($user->getReportable()) {
                $mail = new NewReservoirDataMail();
                $mail->prepare($user->getEmail(), []);
                $this->mailerService->send($mail);
            }
        }
    }

    public function onNewUserNotification(NewUserNotificationEvent $event): void
    {
        $mail = new NewUserMail();
        $mail->prepare(
            $event->getEmail(),
            ['email' => $event->getEmail(), 'password' => $event->getPassword()]
        );
        $this->mailerService->send($mail);
    }
}
