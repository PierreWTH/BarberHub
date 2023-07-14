<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "Rendez-vous invalide. ")]
    private ?\DateTimeInterface $debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fin = null;

    #[ORM\ManyToMany(targetEntity: BarberPrestation::class, inversedBy: 'rendezVouses')]
    private Collection $barberprestation;

    #[ORM\ManyToOne(inversedBy: 'rendezVouses')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'rendezvouses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Merci de choisir qui effectuera la prestation. ")]
    private ?Personnel $personnel = null;

    public function __construct()
    {
        $this->barberprestation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDebut(): ?\DateTimeInterface
    {
        return $this->debut;
    }

    public function setDebut(\DateTimeInterface $debut): self
    {
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?\DateTimeInterface
    {
        return $this->fin;
    }

    public function setFin(\DateTimeInterface $fin): self
    {
        $this->fin = $fin;

        return $this;
    }

    /**
     * @return Collection<int, BarberPrestation>
     */
    public function getBarberPrestation(): Collection
    {
        return $this->barberprestation;
    }

    public function addBarberPrestation(BarberPrestation $barberprestation): self
    {
        if (!$this->barberprestation->contains($barberprestation)) {
            $this->barberprestation->add($barberprestation);
        }

        return $this;
    }

    public function removeBarberPrestation(BarberPrestation $barberprestation): self
    {
        $this->barberprestation->removeElement($barberprestation);

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPersonnel(): ?Personnel
    {
        return $this->personnel;
    }

    public function setPersonnel(?Personnel $personnel): self
    {
        $this->personnel = $personnel;

        return $this;
    }
}
