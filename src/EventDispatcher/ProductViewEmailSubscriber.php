<?php

namespace App\EventDispatcher;

use Psr\Log\LoggerInterface;
use App\Event\ProductViewEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class ProductViewEmailSubscriber implements EventSubscriberInterface 
{
    protected $logger;
    protected $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->Mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendEmail'
        ];
    }

    public function sendEmail() 
    {
        // $email = new TemplatedEmail();

        // $email->from(new Address("contact@mail.com", "Mes réceptions d'emails"))
        //     ->to("admin@mail.com")
        //     ->text("Un mail nous est envoyé")
        //     ->htmlTemplate('emails/mail_view.html.twig')
        //     ->subject("Pour l'exemple");

        // $this->mailer->send($email);



        $this->logger->info("Email envoyé à l'admin pour le produit");
    }

}