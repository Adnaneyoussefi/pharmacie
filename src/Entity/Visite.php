<?php

namespace App\Entity;

use App\Repository\VisiteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VisiteRepository::class)
 */
class Visite
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $nb_visite;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbVisite(): ?int
    {
        return $this->nb_visite;
    }

    public function setNbVisite(int $nb_visite): self
    {
        $this->nb_visite = $nb_visite;

        return $this;
    }
}
