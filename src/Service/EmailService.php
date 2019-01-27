<?php
// src/Service/EmailService.php
namespace App\Service;

use Twig\Environment;

class EmailService
{
    protected $mailer;

    private $twig;

    public function __construct(\Swift_Mailer $mailer,Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendEmail()
    {
        $message = (new \Swift_Message('Hello Email'))
        ->setFrom('projetopenclassroom@gmail.com')
        ->setTo('projetopenclassroom@gmail.com')
        ->setBody(
            $this->twig->render(
                // templates/emails/registration.html.twig
                'base.html.twig'/*,
                ['name' => $name]*/
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