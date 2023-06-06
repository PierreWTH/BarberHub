<?php

namespace App\Entity;

use App\Repository\PrestationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrestationRepository::class)]
class Prestation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 70)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'prestation', targetEntity: BarberPrestation::class)]
    private Collection $barberPrestations;

    public function __construct()
    {
        $this->barberPrestations = new ArrayCollection();
    }

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

    public function __toString()
    {
        return $this->nom;
    }

    /**
     * @return Collection<int, BarberPrestation>
     */
    public function getBarberPrestations(): Collection
    {
        return $this->barberPrestations;
    }

    public function addBarberPrestation(BarberPrestation $barberPrestation): self
    {
        if (!$this->barberPrestations->contains($barberPrestation)) {
            $this->barberPrestations->add($barberPrestation);
            $barberPrestation->setPrestation($this);
        }

        return $this;
    }

    public function removeBarberPrestation(BarberPrestation $barberPrestation): self
    {
        if ($this->barberPrestations->removeElement($barberPrestation)) {
            // set the owning side to null (unless already changed)
            if ($barberPrestation->getPrestation() === $this) {
                $barberPrestation->setPrestation(null);
            }
        }

        return $this;
    }
}
