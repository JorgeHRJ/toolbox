<?php

namespace App\Service;

use App\Library\Mail\AbstractMail;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService
{
    private string $from;
    private MailerInterface $mailer;
    private Environment $templating;
    private LoggerInterface $logger;

    public function __construct(
        string $from,
        MailerInterface $mailer,
        Environment $templating,
        LoggerInterface $logger
    ) {
        $this->from = $from;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->logger = $logger;
    }

    public function send(AbstractMail $mailData): void
    {
        $email = new Email();
        try {
            $email
                ->from($this->from)
                ->to($mailData->getTo())
                ->subject($mailData->getSubject())
                ->html($this->templating->render($mailData->getTemplate(), $mailData->getParameters()));
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    'Email (type %s) to %s was not sent due to a templating error: %s',
                    get_class($mailData),
                    $mailData->getTo(),
                    $e->getMessage()
                )
            );
        }

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error(
                sprintf(
                    'Email (type %s) to %s was not sent due to a transport error: %s',
                    get_class($mailData),
                    $mailData->getTo(),
                    $e->getMessage()
                )
            );
        }
    }
}
