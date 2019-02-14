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
    
    public function StripeCheckIn($orders)
    {
        \Stripe\Stripe::setApiKey("sk_test_OBYLdnEywNxjtYmtslFnKy7E");

        // Token is created using Checkout or Elements!
        // Get the payment token ID submitted by the form:
        $token = $_POST['stripeToken'];
        try { 
            $charge = \Stripe\Charge::create([
                'amount' => $this->SumTicket($orders),
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
    public function SumTicket($orders)
    {
        $tickets = $orders->getTicketsId();
        $TotalPrice = 0;
        
        foreach ($tickets as $ticket) {
            $TotalPrice = $TotalPrice + $ticket->getPrice();
            }
            return $TotalPrice;
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
   public function getHolidays($orders)
    {
      $date = $orders->GetDate()->format('Y-m-d');
      $date = strtotime($date);

      $year = intval($orders->GetDate()->format('Y'));
  
  
    $easterDate  = mktime(0, 0, 0, 3,  21,  $year) + ( 24 *3600 * easter_days($year));
    $easterDay   = intval(date('j', $easterDate));
    $easterMonth = intval(date('n', $easterDate));
    $easterYear   = intval(date('Y', $easterDate));
    
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
      mktime(0, 0, 0, $easterMonth, $easterDay + 1,  $easterYear), // Lundi de paques
      mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear),  // Ascension
      mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear), // Pentecote
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
  public function isValidDay($orders)
  {
    $date = $orders->GetDate()->format('Ymd');
    $now = date("Ymd");
    $hours = date("H");
    $tickets = $orders->GetTicketsId();
    foreach($tickets as $ticket){
    $allDay = $ticket->GetAllday();
    if($date == $now AND $hours >= 14 AND $allDay == 1)
    {
      return "Vous ne pouvez pas réserver pour toute la journée après 14 heures.";
    }
  }
   if($date < $now )
    {
      return "Vous ne pouvez pas réserver pour les jours passés.";
    }
  }
    public function RandomToken($orders)
    {

      //Generate a random string.
      $token = openssl_random_pseudo_bytes(8);
      
      //Convert the binary data into hexadecimal representation.
      $token = bin2hex($token);

      $orders->SetToken($token);
      $tickets = $orders->GetTicketsId();

      foreach($tickets as $ticket){
        $token = openssl_random_pseudo_bytes(8);
      
        //Convert the binary data into hexadecimal representation.
        $token = bin2hex($token);
        $ticket->SetToken($token);
      }
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
}