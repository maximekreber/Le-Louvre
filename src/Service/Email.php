<?php
// src/Service/Email.php
namespace App\Service;

class Email
{
    private $mailer;
    private $templating;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $templating)
    {
        $this->mailer     = $mailer;
        $this->templating = $templating;
    }

    public function sendEmail()
    {
        $message = (new \Swift_Message('Hello Email'))
        ->setFrom('projetopenclassroom@gmail.com')
        ->setTo('projetopenclassroom@gmail.com')
        ->setBody(
            $this->renderView(
                // templates/emails/registration.html.twig
                'emails/registration.html.twig',
                ['name' => $name]
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

    $mailer->send($message);

        $message = $this->templating->render('order/payement.html.twig');

        // ...
    }
}