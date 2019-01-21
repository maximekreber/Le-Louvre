<?php

namespace App\Controller;

use App\Entity\Tickets;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
use App\Service\Email;

class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="order")
     */
    public function index(Request $request)
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
    public function stripe(Request $request)
    {
        $id = $request->query->get('id');
        $idint = intval($id);
        var_dump($idint);

        return $this->render(
            '/order/stripe.html.twig'
                );
    }

    /**
     * @Route("/payement", name="payement")
     */

    public function payement(Request $request,Email $Email)
    {
        \Stripe\Stripe::setApiKey("sk_test_4eC39HqLyjWDarjtT1zdp7dc");

        // Token is created using Checkout or Elements!
        // Get the payment token ID submitted by the form:
        $token = $_POST['stripeToken'];
        
        $charge = \Stripe\Charge::create([
            'amount' => 999,
            'currency' => 'eur',
            'description' => 'Example charge',
            'source' => $token,
        ]);

        return $this->render(
            '/order/payement.html.twig'
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
