<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrdersRepository")
 */
class Orders
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tickets", mappedBy="order_id", orphanRemoval=true, cascade={"persist"})
     */
    private $tickets_id;

    public function __construct()
    {
        $this->tickets_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return Collection|Tickets[]
     */
    public function getTicketsId(): Collection
    {
        return $this->tickets_id;
    }

    public function addTicketsId(Tickets $ticketsId): self
    {
        if (!$this->tickets_id->contains($ticketsId)) {
            $this->tickets_id[] = $ticketsId;
            $ticketsId->setOrderId($this);
        }

        return $this;
    }

    public function removeTicketsId(Tickets $ticketsId): self
    {
        if ($this->tickets_id->contains($ticketsId)) {
            $this->tickets_id->removeElement($ticketsId);
            // set the owning side to null (unless already changed)
            if ($ticketsId->getOrderId() === $this) {
                $ticketsId->setOrderId(null);
            }
        }

        return $this;
    }
}
