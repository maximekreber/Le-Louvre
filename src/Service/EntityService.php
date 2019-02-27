<?php 
// src/Service/EntityService.php
namespace App\Service;

use App\Entity\Tickets;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class EntityService 
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    } 
    public function Check1000Ticket($orders)
    {
      $repository = $this->entityManager->getRepository(Tickets::class);
      $tickets = $orders->getTicketsId();
      $ordersdate = $orders->GetDate();
      $date = $repository->findByDate($ordersdate);
      $count = count($date) + count($tickets);
      
        if($count >= 1000)
      {
        return "Il n'y a plus de ticket disponible à cette date.Veuillez choisir une autre date";
      }
    }  

}