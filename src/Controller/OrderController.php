<?php

namespace App\Controller;

use App\Entity\Tickets;

use Symfony\Component\Form\Forms;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Orders;
use App\Form\OrdersType;
use App\Form\TicketsType;
use App\Service\EmailService;
use App\Service\OrderService;
use App\Service\EntityService;
use Symfony\Component\HttpFoundation\Session\Session; 

class OrderController extends AbstractController
{
    /**
     * @Route("/", name="order")
     */
    public function index(Request $request,Session $session)
    {
        // 1) build the form
        
        $orders = new Orders();
        $form = $this->createForm(OrdersType::class, $orders);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('order', $orders);
            
            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user
            return $this->redirectToRoute('stripe');
        }

        return $this->render(
            '/order/index.html.twig',
            array('form' => $form->createView(),
                ));
    }
    /**
     * @Route("/stripe", name="stripe")
     */
    public function stripe(Request $request,OrderService $OrderService,Session $session,EntityService $EntityService)
    {
     
        $orders = $session->get('order');
        $error4 = $OrderService->EmptyTicket($orders);
        $alreadypaid = $orders->getId();
        $OrderService->SetTicketDate($orders);
        $OrderService->TicketPrice($orders);
     
        $error2 = $EntityService->Check1000Ticket($orders);
        $email = $orders->GetEmail();
       
        $error1 = $OrderService->getHolidays($orders);
        $error3 = $OrderService->isValidDay($orders);
        $OrderService->RandomToken($orders);

        if(isset($alreadypaid))
        {
            $this->addFlash('error', "Vous avez déjà payé votre commande. Les tickets ont été envoyés dans votre boîte mail $email");
            return $this->redirectToRoute('order', ['_fragment' => 'ticket']);
        }

        if(isset($error1) OR isset($error2) OR isset($error3) OR isset($error4))
        {
            $this->addFlash('error', "$error1 $error2 $error3 $error4");
            return $this->redirectToRoute('order',['_fragment' => 'ticket']);
        }
        $email = $orders->GetEmail();
        $TotalPrice = $OrderService->SumTicket($orders);
         
        return $this->render(
            '/order/stripe.html.twig',array(
                'Price' => $TotalPrice,
                'Email' => $email
            )
                );
    }

    /**
     * @Route("/payement", name="payement")
     */

    public function payement(Request $request,OrderService $OrderService,Session $session,EmailService $EmailService)
    {
        
        $orders = $session->get('order');
        $alreadypaid = $orders->getId();
     
        $email = $orders->GetEmail();
        $error = $OrderService->StripeCheckIn($orders);

        if(isset($alreadypaid))
        {
            $this->addFlash('error', "Vous avez déjà payé votre commande. Les tickets ont été envoyés dans votre boîte mail $email");
            return $this->redirectToRoute('order', ['_fragment' => 'ticket']);
        }

        if(isset($error))
        {
            $this->addFlash('error', $error);
            return $this->redirectToRoute('stripe');
        }
            $EmailService->sendEmail($orders);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($orders);
            $entityManager->flush();
            


        return $this->render(
            '/order/payement.html.twig',array(
                'email' => $email
            )
                );
    }
    
}
