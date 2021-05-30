<?php

namespace App\Library\Event;

use Symfony\Contracts\EventDispatcher\Event;

class IrrigationNotificationEvent extends Event
{
    const EVENT = 'toolbox.notification_event.irrigation';
}
