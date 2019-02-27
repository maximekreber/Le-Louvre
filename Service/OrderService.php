<?php

namespace Service;


class OrderService
{
    public function SumTicket($orders)
    {
        $tickets = $orders->getTicketsId();
        $TotalPrice = 0;
        
        foreach ($tickets as $ticket) {
            $TotalPrice = $TotalPrice + $ticket->getPrice();
            }
            return $TotalPrice;
    }
    public function SetTicketDate($orders)
    {
      $tickets = $orders->GetTicketsId();
      $date = $orders->GetDate();
      foreach($tickets as $ticket){
          $ticket->SetDate($date);
      }
    }
    public function EmptyTicket($orders)
    {
      $ticket = $orders->GetTicketsID();
      if(!isset($ticket[0]))
      {
        return "Vous devez ajouter un ticket à votre commande";
      }
    }
    public function TicketPrice($orders)
    {
    
      $tickets = $orders->getTicketsId();
        foreach($tickets as $ticket){
          $date = $ticket->GetBirthDate()->format('Y-m-d');
          $date = strtotime($date);
          $date1 = $ticket->GetDate()->format('Y-m-d');
          $date1 = strtotime($date1);
          $datetime1 = new \DateTime();                // visitdate
          $datetime2 = new \DateTime();                // birthdate

          $datetime1->setTimestamp($date1);
          $datetime2->setTimestamp($date);

          $interval = $datetime2->diff($datetime1);
          $reduced = $ticket->GetReduced();
          $yo = $interval->y; 

        if($reduced == 1)//reduced
          {
            $ticket->SetPrice(1000);
          }
           elseif($yo <= 4){//baby
            $ticket->SetPrice(0);
           }
           elseif($yo <= 12){//child
            $ticket->SetPrice(800);
          }
          elseif($yo >= 60){//senior
            $ticket->SetPrice(1200);
          }
          else{//normal
            $ticket->SetPrice(1600);
          }
    }
  }
}