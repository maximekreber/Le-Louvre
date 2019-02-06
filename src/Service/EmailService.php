<?php
// src/Service/EmailService.php
namespace App\Service;

use Twig\Environment;

class EmailService
{
    private $mailer;

    private $twig;

    public function __construct(\Swift_Mailer $mailer,Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendEmail($orders)
    {
        $email = $orders->getEmail();
        $message = (new \Swift_Message('Ticket Musée du Louvre'))
        ->setFrom('projetopenclassroom@gmail.com')
        ->setTo($email)
        ->setBody(
            $this->twig->render(
                // templates/emails/index.html.twig
                'email/index.html.twig',
                ['orders' => $orders]
            ),
            'text/html'
        )
        /*
         * If you also want to include a plaintext version of the message
        ->addPart(
            $this->renderView(
                'emails/registration.txt.twig',
                ['name' => $name]
            ),
            'text/plain'
        )
        */
    ;

    return $this->mailer->send($message);

    }
}