<?php 
// src/Service/OrderService.php
namespace App\Service;

use App\Entity\Tickets;
use Doctrine\ORM\EntityManagerInterface;

class OrderService 
{
    protected $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }   
    
    public function StripeCheckIn()
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
    }
    public function SumTicket($orderid)
    {
        $repository = $this->entityManager->getRepository(Tickets::class);
        $ticketsid = $repository->findByOrderId($orderid);
        $TotalPrice = 0;
        
        foreach ($ticketsid as $ticketstest) {
            $TotalPrice = $TotalPrice + $ticketstest->getPrice();
            var_dump($TotalPrice);
            }
            return $TotalPrice;
    }
}