<?php

namespace App\Library\Mail;

class NewReservoirDataMail extends AbstractMail
{
    protected function getMailTemplate(): string
    {
        return 'mail/new_reservoir_data.html.twig';
    }

    protected function getMailSubject(): string
    {
        return 'Toolbox - Nuevos datos de Balsas añadidos';
    }
}
