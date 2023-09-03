<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PersonnelRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Ignore;


#[ORM\Entity(repositoryClass: PersonnelRepository::class)]
class Personnel
{   
    #[Ignore]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Ignore]
    #[ORM\Column]
    private ?bool $manager = null;

    #[Ignore]
    #[ORM\ManyToOne(inversedBy: 'personnels')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Barbershop $barbershop = null;

    #[Ignore]
    #[ORM\OneToOne(inversedBy: 'personnel', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: RendezVous::class)]
    private Collection $rendezvouses;

    public function __construct()
    {
        $this->rendezvouses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isManager(): ?bool
    {
        return $this->manager;
    }

    public function setManager(bool $manager): self
    {
        $this->manager = $manager;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Rendezvous>
     */
    public function getRendezvouses(): Collection
    {
        return $this->rendezvouses;
    }

    public function addRendezvouse(Rendezvous $rendezvouse): self
    {
        if (!$this->rendezvouses->contains($rendezvouse)) {
            $this->rendezvouses->add($rendezvouse);
            $rendezvouse->setPersonnel($this);
        }

        return $this;
    }

    public function removeRendezvouse(Rendezvous $rendezvouse): self
    {
        if ($this->rendezvouses->removeElement($rendezvouse)) {
            // set the owning side to null (unless already changed)
            if ($rendezvouse->getPersonnel() === $this) {
                $rendezvouse->setPersonnel(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->user;
    }
}
