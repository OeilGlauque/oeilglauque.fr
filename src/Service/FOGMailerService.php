<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class FOGMailerService
{

    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendMail(Address $to, String $subject, String $template, array $context) : bool
    {
        $email = (new TemplatedEmail())
            ->from(new Address("fogfogtest@gmail.com","L'équipe du FOG"))
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);

        $this->mailer->send($email);

        return true;
    }
}