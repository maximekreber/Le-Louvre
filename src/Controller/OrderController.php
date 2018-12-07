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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="order")
     */
    public function index(Request $request)
    {
        // 1) build the form
        $orders = new Orders();
        $tickets = new Tickets();
        $req = compact($orders , $tickets);
        $form = $this->createFormBuilder($req)
            ->add('email', TextType::class)
            ->add('date', DateType::class)
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('birthdate', DateType::class)
            ->add('allday', CheckboxType::class)
            ->add('reduced', CheckboxType::class)
            ->add('save', SubmitType::class, array('label' => 'Commander'))
            ->getForm();

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {




            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($orders);
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
