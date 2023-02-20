<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class FOGMailerService
{

    private MailerInterface $mailer;
    private Address $mailFOG;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->mailFOG = new Address("fogfogtest@gmail.com", "L'équipe du FOG");
    }

    public function sendMail(Address $to, String $subject, String $template, array $context, array $cc = [], array $bcc = []) : bool
    {
        $email = (new TemplatedEmail())
            ->from($this->mailFOG)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context)
            ->cc(...$cc)
            ->bcc(...$bcc);

        $this->mailer->send($email);

        return true;
    }

    public function getMailFOG(): Address 
    {
        return $this->mailFOG;
    }
}