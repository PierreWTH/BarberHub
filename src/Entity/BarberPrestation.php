<?php

namespace App\Entity;

use App\Repository\BarberPrestationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BarberPrestationRepository::class)]
class BarberPrestation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\ManyToOne(inversedBy: 'barberPrestations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Barbershop $barbershop = null;

    #[ORM\ManyToOne(inversedBy: 'barberPrestations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Prestation $prestation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getBarbershop(): ?Barbershop
    {
        return $this->barbershop;
    }

    public function setBarbershop(?Barbershop $barbershop): self
    {
        $this->barbershop = $barbershop;

        return $this;
    }

    public function getPrestation(): ?Prestation
    {
        return $this->prestation;
    }

    public function setPrestation(?Prestation $prestation): self
    {
        $this->prestation = $prestation;

        return $this;
    }
}
