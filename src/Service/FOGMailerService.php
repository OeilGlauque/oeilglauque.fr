<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\SendmailTransport;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class FOGMailerService
{

    private Mailer $mailer;
    private Address $mailFOG;
    private BodyRenderer $twigRender;

    public function __construct()
    {
        $this->mailer = new Mailer(new SendmailTransport( '/usr/sbin/sendmail -t' ));
        $this->mailFOG = new Address("fogfogtest@gmail.com", "L'équipe du FOG");
        $this->twigRender = new BodyRenderer(new Environment(new FilesystemLoader('../templates')));
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

        $this->twigRender->render($email);

        $this->mailer->send($email);

        return true;
    }

    public function getMailFOG(): Address 
    {
        return $this->mailFOG;
    }
}