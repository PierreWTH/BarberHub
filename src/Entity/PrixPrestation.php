<?php

namespace App\Entity;

use App\Repository\PrixPrestationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrixPrestationRepository::class)]
class PrixPrestation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'prixPrestations')]
    private ?Prestation $prestation = null;

    #[ORM\ManyToOne(inversedBy: 'prixPrestations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Barbershop $barbershop = null;

    #[ORM\Column]
    private ?float $prix = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBarbershop(): ?Barbershop
    {
        return $this->barbershop;
    }

    public function setBarbershop(?Barbershop $barbershop): self
    {
        $this->barbershop = $barbershop;

        return $this;
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
}
