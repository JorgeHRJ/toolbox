<?php

namespace App\Library\Mail;

class NewUserMail extends AbstractMail
{
    /**
     * @return string
     */
    protected function getMailTemplate(): string
    {
        return 'mail/new_user.html.twig';
    }

    /**
     * @return string
     */
    protected function getMailSubject(): string
    {
        return 'Usuario en Toolbox';
    }
}
