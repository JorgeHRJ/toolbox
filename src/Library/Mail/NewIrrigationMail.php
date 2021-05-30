<?php

namespace App\Library\Mail;

class NewIrrigationMail extends AbstractMail
{
    protected function getMailTemplate(): string
    {
        return 'mail/new_irrigation.html.twig';
    }

    protected function getMailSubject(): string
    {
        return 'Toolbox - Nueva Recomendación de Riego añadida';
    }
}
