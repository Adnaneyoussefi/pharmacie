<?php

namespace App\Entity;

use App\Repository\TaallaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaallaRepository::class)
 */
class Taalla
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $salwa;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSalwa(): ?string
    {
        return $this->salwa;
    }

    public function setSalwa(string $salwa): self
    {
        $this->salwa = $salwa;

        return $this;
    }
}
