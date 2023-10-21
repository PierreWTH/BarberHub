<?php

namespace App\Entity;

use ORM\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PrestationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: PrestationRepository::class)]
#[HasLifecycleCallbacks]

class Prestation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 70)]
    #[Assert\NotBlank(message: "Le nom ne peux pas être vide. ")]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'prestation', targetEntity: BarberPrestation::class, orphanRemoval: true, cascade:['persist', 'remove'])]
    private Collection $barberPrestations;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    public function __construct()
    {
        $this->barberPrestations = new ArrayCollection();
    }

    // Générer le slug à chaque ajout de prestation
    #[ORM\PrePersist]
    public function prePersist()
    {
        $this->slug = (new Slugify())->slugify($this->nom);
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
