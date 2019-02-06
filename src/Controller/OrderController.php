<?php

namespace App\Controller;

use App\Entity\Tickets;
//use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Forms;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Orders;
use App\Form\OrdersType;
use App\Form\TicketsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use App\Service\EmailService;
use App\Service\OrderService;
use Symfony\Component\HttpFoundation\Session\Session; 

class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="order")
     */
    public function index(Request $request,Session $Session)
    {
        // 1) build the form
        $orders = new Orders();
        $form = $this->createForm(OrdersType::class, $orders);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($orders);
            $entityManager->flush();
            $entityManager->refresh($orders);
        

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user
            return $this->redirectToRoute('stripe', array('id'=>$orders->getId()));
        }

        return $this->render(
            '/order/index.html.twig',
            array('form' => $form->createView(),
                ));
    }
    /**
     * @Route("/stripe", name="stripe")
     */
    public function stripe(Request $request,OrderService $OrderService)
    {
        $id = $request->query->get('id');
        $idint = intval($id);
        var_dump($idint);
        $OrderService->TicketPrice($idint);
        $error2 = $OrderService->Check1000Ticket($idint);

        $repository = $this->getDoctrine()->getRepository(Orders::class);
        $orderid = $repository->find($idint);
        $error1 = $OrderService->getHolidays($orderid);
        $error3 = $OrderService->isValidDay($orderid);
       
        if(isset($error1) OR isset($error2) OR isset($error3))
        {
            $this->addFlash('error', "$error1 $error2 $error3");
            return $this->redirectToRoute('order');
        }
        $OrderService->TicketPrice($idint);
        $repository = $this->getDoctrine()->getRepository(Tickets::class);
        $ticketsid = $repository->findByOrderId($idint);
        $TotalPrice = 0;

        $repository = $this->getDoctrine()->getRepository(Orders::class);
        $orderid = $repository->find($idint);

        $email = $orderid->GetEmail();
        foreach ($ticketsid as $ticketstest) {
            $TotalPrice = $TotalPrice + $ticketstest->getPrice();
            var_dump($TotalPrice);
            }
        
        return $this->render(
            '/order/stripe.html.twig',array(
                'idint' => $idint,
                'Price' => $TotalPrice,
                'Email' => $email
            )
                );
    }

    /**
     * @Route("/payement", name="payement")
     */

    public function payement(Request $request,OrderService $OrderService)
    {
        

        $id = $request->query->get('id');
        $idint = intval($id);
        $error = $OrderService->StripeCheckIn($idint);
        

        if(isset($error))
        {
            $this->addFlash('error', $error);
            return $this->redirectToRoute('stripe', array('id'=> $idint));
        }
        $repository = $this->getDoctrine()->getRepository(Tickets::class);
        $ticketsid = $repository->findByOrderId($idint);

        $OrderService->SumTicket($idint);

        return $this->render(
            '/order/payement.html.twig',array(
                'tickets' => $ticketsid
            )
                );
    }
    
    /**
     * @Route("/test", name="test")
     */

    public function test(Request $request,\Swift_Mailer $mailer)
    {
        $idt = $request->query->get('id');
        $idint = intval($idt);
        var_dump($idint);

        $repository = $this->getDoctrine()->getRepository(Tickets::class);
        $ticketsid = $repository->findByOrderId($idint);

        foreach ($ticketsid as $ticketstest) {
        var_dump($ticketstest->getId());
        }

        $message = (new \Swift_Message('Hello Email'))
        ->setFrom('projetopenclassroom@gmail.com')
        ->setTo('projetopenclassroom@gmail.com')
        ->setBody(
            $this->renderView(
                // templates/emails/registration.html.twig
                'order/payement.html.twig',array(
                    'tickets' => $ticketsid
                )
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

        return $this->render(
            '/order/payement.html.twig',array(
                'tickets' => $ticketsid
            )
                );
    }
     /**
     * @Route("/testcalcul", name="testcalcul")
     */
    public function testcalcul(Request $request,OrderService $OrderService,EmailService $EmailService)
    {
        $idt = $request->query->get('id');
        $idint = intval($idt);
        var_dump($idint);

        $EmailService->SendEmail();
        $OrderService->SumTicket($idint);

        return $this->render(
            'base.html.twig'
                );
    }
    /**
     * @Route("/ticket", name="ticket")
     */
    public function ticket(Request $request)
    {
        // 1) build the form



        $nbper = $request->query->get('nbper');
        $nbper = intval($nbper);
       /* $id = $request->query->get('id');
        $idint = intval($id);
        var_dump($idint);
        $repository = $this->getDoctrine()->getRepository(Orders::class);
        $orders = $repository->find(13);*/

        //var_dump($orders);
        //$order = $tickets->setOrderId($idint);
        $tickets = new Tickets();
        $form = $this->createFormBuilder($tickets)
            ->add('date', DateType::class)
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('birthdate', BirthdayType::class)
            ->add('allday', CheckboxType::class)
            ->add('reduced', CheckboxType::class)
            ->add('price', CheckboxType::class)
            ->add('token', CheckboxType::class)
            ->add('order_id', EntityType::class, array(
                'class' => Orders::class,
                'choice_label' => 'id',))
            ->add('save', SubmitType::class, array('label' => 'Commander'))
            ->getForm();
            
        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {




            $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($orders);
            $entityManager->persist($tickets);
            $entityManager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('');
        }

        return $this->render(
            '/order/index.html.twig',
            array('form' => $form->createView(),
            'nbper' => $nbper,
            ));
    }
}
