<?php

namespace App\Library\Mail;

class NewUserMail extends AbstractMail
{
    public function __construct()
    {
    }

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
        return 'Toolbox - Usuario en la aplicación';
    }
}
