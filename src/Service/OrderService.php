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
}