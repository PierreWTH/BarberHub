<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Repository\BarberPrestationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BarberPrestationRepository::class)]
class BarberPrestation
{   
    #[Ignore]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Ignore]
    #[ORM\Column]
    #[Assert\NotBlank(message: "Le prix ne peut pas être vide. ")]
    private ?float $prix = null;

    #[Ignore]
    #[ORM\ManyToOne(inversedBy: 'barberPrestations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Barbershop $barbershop = null;

    #[Ignore]
    #[ORM\ManyToOne(inversedBy: 'barberPrestations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Prestation $prestation = null;

    #[Ignore]
    #[ORM\ManyToMany(targetEntity: RendezVous::class, mappedBy: 'barberprestation')]
    private Collection $rendezVouses;

    public function __construct()
    {
        $this->rendezVouses = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, RendezVous>
     */
    public function getRendezVouses(): Collection
    {
        return $this->rendezVouses;
    }

    public function addRendezVouse(RendezVous $rendezVouse): self
    {
        if (!$this->rendezVouses->contains($rendezVouse)) {
            $this->rendezVouses->add($rendezVouse);
            $rendezVouse->addBarberprestation($this);
        }

        return $this;
    }

    public function removeRendezVouse(RendezVous $rendezVouse): self
    {
        if ($this->rendezVouses->removeElement($rendezVouse)) {
            $rendezVouse->removeBarberprestation($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->prestation;
    }
}
