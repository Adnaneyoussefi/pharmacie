<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdminRepository")
 */
class Admin
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Client", inversedBy="admins")
     */
    private $clients;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Proprietaire", inversedBy="admins")
     */
    private $proprietaires;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="admin", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
        $this->proprietaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Client[]
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->contains($client)) {
            $this->clients->removeElement($client);
        }

        return $this;
    }

    /**
     * @return Collection|Proprietaire[]
     */
    public function getProprietaires(): Collection
    {
        return $this->proprietaires;
    }

    public function addProprietaire(Proprietaire $proprietaire): self
    {
        if (!$this->proprietaires->contains($proprietaire)) {
            $this->proprietaires[] = $proprietaire;
        }

        return $this;
    }

    public function removeProprietaire(Proprietaire $proprietaire): self
    {
        if ($this->proprietaires->contains($proprietaire)) {
            $this->proprietaires->removeElement($proprietaire);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
