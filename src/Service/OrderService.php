<?php 
// src/Service/OrderService.php
namespace App\Service;

use App\Entity\Tickets;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class OrderService 
{
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }   
    
    public function StripeCheckIn($id)
    {
        \Stripe\Stripe::setApiKey("sk_test_OBYLdnEywNxjtYmtslFnKy7E");

        // Token is created using Checkout or Elements!
        // Get the payment token ID submitted by the form:
        $token = $_POST['stripeToken'];
        try { 
            $charge = \Stripe\Charge::create([
                'amount' => $this->SumTicket($id),
                'currency' => 'eur',
                'description' => 'Paiement des tickets',
                'source' => $token,
            ]);
    }   catch(\Stripe\Error\Card $e) {
        // Since it's a decline, \Stripe\Error\Card will be caught
        $body = $e->getJsonBody();
        $err  = $body['error'];
        print('Status is:' . $e->getHttpStatus() . "\n");
        print('Type is:' . $err['type'] . "\n");
        print('Code is:' . $err['code'] . "\n");

        print('Message is:' . $err['message'] . "\n");
        return $err['message'];
      } catch (\Stripe\Error\RateLimit $e) {
        return "Suite à un grand nombre de requête nous ne pouvons pas accéder à votre demande ,veuillez ressayer ultérieurement";// Too many requests made to the API too quickly
      } catch (\Stripe\Error\InvalidRequest $e) {
        return "Requête invalide";
      } catch (\Stripe\Error\Authentication $e) {
        return "Problème de connection à stripe.Veuillez ressayer plus tard.";
      } catch (\Stripe\Error\ApiConnection $e) {
        return "Problème de connection à stripe.Veuillez ressayer plus tard.";// Network communication with Stripe failed
      } catch (\Stripe\Error\Base $e) {
        return "Un problème est survenu veuillez ressayer";// Display a very generic error to the user, and maybe send
        // yourself an email
      } catch (Exception $e) {
        return "Une erreur est survenu veuillez ressayer";// Something else happened, completely unrelated to Stripe
      }
    }
    public function SumTicket($orderid)
    {
        $repository = $this->entityManager->getRepository(Tickets::class);
        $ticketsid = $repository->findByOrderId($orderid);
        $TotalPrice = 0;
        
        foreach ($ticketsid as $ticketstest) {
            $TotalPrice = $TotalPrice + $ticketstest->getPrice();
            }
            return $TotalPrice;
    }
    public function Check1000Ticket($orderid)
    {
      $repository = $this->entityManager->getRepository(Tickets::class);
      $ticketsid = $repository->findByOrderId($orderid);

      $tickets0 = $ticketsid['0'];
      $ticketdate = $tickets0->GetDate();
      $date = $repository->findByDate($ticketdate);
      $count = count($date);
        if($count >= 1000)
      {
        return "Il n'y a plus de ticket disponible à cette date.Veuillez choisir une autre date";
      }
    }
    public function TicketPrice($orderid)
    {
      $repository = $this->entityManager->getRepository(Tickets::class);
      $ticketsid = $repository->findByOrderId($orderid);
        foreach($ticketsid as $ticket){
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

        if($reduced = 1)//reduced
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
   public function getHolidays($orders)
    {
      $date = $orders->GetDate()->format('Y-m-d');
      $date = strtotime($date);

      $year = $orders->GetDate()->format('Y');
    
  
    $easterDate  = easter_date($year);
    $easterDay   = date('j', $easterDate);
    $easterMonth = date('n', $easterDate);
    $easterYear   = date('Y', $easterDate);
  
    $holidays = array(
      // Dates fixes
      mktime(0, 0, 0, 1,  1,  $year),  // 1er janvier
      mktime(0, 0, 0, 5,  1,  $year),  // Fête du travail
      mktime(0, 0, 0, 5,  8,  $year),  // Victoire des alliés
      mktime(0, 0, 0, 7,  14, $year),  // Fête nationale
      mktime(0, 0, 0, 8,  15, $year),  // Assomption
      mktime(0, 0, 0, 11, 1,  $year),  // Toussaint
      mktime(0, 0, 0, 11, 11, $year),  // Armistice
      mktime(0, 0, 0, 12, 25, $year),  // Noel
  
      // Dates variables
      mktime(0, 0, 0, $easterMonth, $easterDay + 1,  $easterYear),
      mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear),
      mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear),
    );
  
    sort($holidays);

    $days = $orders->GetDate()->format('w');
    $days = intval($days);
    
    if($days == 0)
    {
      return "Le musée n'est pas ouvert les dimanches.";
    }
    if($days == 2)
    {
      return "Le musée n'est pas ouvert les mardis.";
    }
    foreach($holidays as $holiday){
      if($date === $holiday){
        return "Le musée n'est pas ouvert à la date que vous avez choisi.";
    }
    }
    
  }
  
}