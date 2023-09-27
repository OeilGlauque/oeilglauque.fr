<?php

namespace App\Service;

use App\Entity\GoogleAuthToken;
use Doctrine\ORM\EntityManagerInterface;
use Google\Service\Gmail;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Google\Client;
use Google\Service\Gmail\Message;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Twig\Environment;

class FOGGmail
{
    private ?Gmail $mailer;
    private Address $mailFOG;
    private Environment $twig;

    public function __construct(EntityManagerInterface $manager, Client $client, string $address_mail, string $address_name, Environment $twig)
    {
        $this->twig = $twig;
        $this->mailFOG = new Address($address_mail, $address_name);

        $token = $manager->getRepository(GoogleAuthToken::class)->findLastToken();

        if ($token != null) {
            
            $client->setAccessToken($token->getToken());
            
            if ($client->isAccessTokenExpired())
            {
                $new_token = $client->fetchAccessTokenWithRefreshToken();
                
                $token->setAccessToken($new_token['access_token']);
                $token->setRefreshToken($new_token['refresh_token']);
                $token->setCreated($new_token['created']);
                $token->setExpiresIn($new_token['expires_in']);
                
                $manager->flush();
            }
            
            $this->mailer = new Gmail($client);
        }
        else
        {
            $this->mailer = null;
        }
    }

    public function sendTemplatedEmail(Address $to, String $subject, String $template, array $context, array $cc = [], array $bcc = [])
    {
        $email = (new TemplatedEmail())
            ->from($this->mailFOG)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context)
            ->cc(...$cc)
            ->bcc(...$bcc);
        
        $body_renderer = new BodyRenderer($this->twig);
        $body_renderer->render($email);
        
        $message = new Message();
        $message->setRaw(strtr(base64_encode($email->toString()),['+' => '-', '/' => '_']));

        $this->mailer->users_messages->send('me',$message);

        return true;
    }

    public function sendEmail(Address $to, String $subject, string $text, array $cc = [], array $bcc = [])
    {
        $email = (new Email())
            ->from($this->mailFOG)
            ->to($to)
            ->subject($subject)
            ->text($text)
            ->cc(...$cc)
            ->bcc(...$bcc);

        $message = new Message();
        $message->setRaw(strtr(base64_encode($email->toString()),['+' => '-', '/' => '_']));

        $this->mailer->users_messages->send('me',$message);

        return true;
    }

    public function getMailFOG(): Address 
    {
        return $this->mailFOG;
    }
}