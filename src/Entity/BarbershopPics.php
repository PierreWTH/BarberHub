<?php

namespace App\Entity;

use App\Repository\BarbershopPicsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BarbershopPicsRepository::class)]
class BarbershopPics
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'barbershopPics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Barbershop $barbershop = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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
}
