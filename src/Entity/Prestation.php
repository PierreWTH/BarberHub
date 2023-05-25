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

    #[ORM\OneToMany(mappedBy: 'prestation', targetEntity: PrixPrestation::class)]
    private Collection $prixPrestations;

    public function __construct()
    {
        $this->prixPrestations = new ArrayCollection();
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

    /**
     * @return Collection<int, PrixPrestation>
     */
    public function getPrixPrestations(): Collection
    {
        return $this->prixPrestations;
    }

    public function addPrixPrestation(PrixPrestation $prixPrestation): self
    {
        if (!$this->prixPrestations->contains($prixPrestation)) {
            $this->prixPrestations->add($prixPrestation);
            $prixPrestation->setPrestation($this);
        }

        return $this;
    }

    public function removePrixPrestation(PrixPrestation $prixPrestation): self
    {
        if ($this->prixPrestations->removeElement($prixPrestation)) {
            // set the owning side to null (unless already changed)
            if ($prixPrestation->getPrestation() === $this) {
                $prixPrestation->setPrestation(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nom;
    }
}
