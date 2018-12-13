<?php

namespace App\Controller;

use App\Entity\Tickets;
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
            $nbper = 5 ;
            return $this->redirectToRoute('ticket', array('nbper' => $nbper, 'id'=>$orders->getId()));
        }

        return $this->render(
            '/order/index.html.twig',
            array('form' => $form->createView(),
                ));
    }
    /**
     * @Route("/ticket/{nbper}/{id}", name="ticket")
     */
    public function ticket(Request $request)
    {
        // 1) build the form
        $tickets = new Tickets();
        $request->query->get('nbper');
        $id = $request->query->get('id');
        $order = getOrderId($id);
        $form = $this->createFormBuilder($tickets)
            ->add('date', DateType::class)
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('birthdate', BirthdayType::class)
            ->add('allday', CheckboxType::class)
            ->add('reduced', CheckboxType::class)
            ->add('price', CheckboxType::class)
            ->add('token', CheckboxType::class)
            ->add('order', HiddenType::class, array(
                'data' => '$id',
            ))
            ->add('save', SubmitType::class, array('label' => 'Commander'))
            ->getForm();

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {




            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tickets);
            $entityManager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('/');
        }

        return $this->render(
            '/order/index.html.twig',
            array('form' => $form->createView(),
            ));
    }
}
