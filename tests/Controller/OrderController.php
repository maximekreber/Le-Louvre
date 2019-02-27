<?php

namespace Tests\Controller;

use App\Entity\Orders;
use App\Entity\Tickets;
use App\Service\OrderService;
use PHPUnit\Framework\TestCase;

class OrderController extends TestCase
{
    public function testTicketDate()
    {
        $OrderService = new OrderService();
        $orders = new Orders();
        $orders->SetEmail('projetopenclassroom@gmail.com');
        $date = new \DateTime('2019-03-11');
        $orders->SetDate($date);

        $ticket1 = new Tickets();
        $ticket2 = new Tickets();


        $orders->addTicketsId($ticket1);
        $orders->addTicketsId($ticket2);

        $OrderService->SetTicketDate($orders);
     

        $this->assertEquals('projetopenclassroom@gmail.com', $orders->getEmail());
        $this->assertEquals($date, $ticket1->getDate());
    }
    public function testTicketBirth()
    {
        $OrderService = new OrderService();
        $orders = new Orders();
        $orders->SetEmail('projetopenclassroom@gmail.com');
        $date = new \DateTime('2019-03-11');
        $birth = new \DateTime('1996-03-11');
        $orders->SetDate($date);

        $ticket1 = new Tickets();
        $ticket2 = new Tickets();
        $ticket1->SetBirthDate($birth);
        $ticket2->SetBirthDate($birth);

        $orders->addTicketsId($ticket1);
        $orders->addTicketsId($ticket2);

        $OrderService->SetTicketDate($orders);
        $OrderService->TicketPrice($orders);
     

        $this->assertEquals($birth, $ticket1->getBirthDate());
        $this->assertEquals($birth, $ticket2->getBirthDate());
    }
    public function testTicketPrice()
    {
        $OrderService = new OrderService();
        $orders = new Orders();
        $orders->SetEmail('projetopenclassroom@gmail.com');
        $date = new \DateTime('2019-03-11');
        $birth = new \DateTime('1996-03-11');
        $orders->SetDate($date);

        $ticket1 = new Tickets();
        $ticket2 = new Tickets();
        $ticket1->SetBirthDate($birth);
        $ticket2->SetBirthDate($birth);
        $ticket1->SetReduced('0');
        $ticket2->SetReduced('0');

        $orders->addTicketsId($ticket1);
        $orders->addTicketsId($ticket2);

        $OrderService->SetTicketDate($orders);
        $OrderService->TicketPrice($orders);
     

        $this->assertEquals('1600', $ticket1->getPrice());
        $this->assertEquals('1600', $ticket2->getPrice());
    }
    public function testTicketSum()
    {
        $OrderService = new OrderService();
        $orders = new Orders();
        $orders->SetEmail('projetopenclassroom@gmail.com');
        $date = new \DateTime('2019-03-11');
        $birth = new \DateTime('1996-03-11');
        $orders->SetDate($date);

        $ticket1 = new Tickets();
        $ticket2 = new Tickets();
        $ticket1->SetBirthDate($birth);
        $ticket2->SetBirthDate($birth);
        $ticket1->SetReduced('0');
        $ticket2->SetReduced('0');

        $orders->addTicketsId($ticket1);
        $orders->addTicketsId($ticket2);

        $OrderService->SetTicketDate($orders);
        $OrderService->TicketPrice($orders);
     

        $this->assertEquals('3200', $OrderService->SumTicket($orders)); 
    }
    public function testTicketEmpty()
    {
        $OrderService = new OrderService();
        $orders = new Orders();
        $orders->SetEmail('projetopenclassroom@gmail.com');
        $date = new \DateTime('2019-03-11');
        $birth = new \DateTime('1996-03-11');
        $orders->SetDate($date);

        $ticket1 = new Tickets();
        $ticket2 = new Tickets();
        $ticket1->SetBirthDate($birth);
        $ticket2->SetBirthDate($birth);
        $ticket1->SetReduced('0');
        $ticket2->SetReduced('0');

        $orders->addTicketsId($ticket1);
        $orders->addTicketsId($ticket2);

        $OrderService->SetTicketDate($orders);
        $OrderService->TicketPrice($orders);
     

        $this->assertEquals(null, $OrderService->EmptyTicket($orders)); 
    }
}