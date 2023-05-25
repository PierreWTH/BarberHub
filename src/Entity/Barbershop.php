<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BarbershopRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: BarbershopRepository::class)]
class Barbershop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 150)]
    private ?string $adresse = null;

    #[ORM\Column(length: 15)]
    private ?string $cp = null;

    #[ORM\Column(length: 45)]
    private ?string $ville = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $horaires = null;

    #[ORM\Column]
    private ?float $latitude = null;

    #[ORM\Column]
    private ?float $longitude = null;

    #[ORM\Column(length: 16)]
    private ?string $telephone = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $instagram = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $facebook = null;

    #[ORM\Column (type: 'boolean', nullable: true, options: ['default' => false])]
    private ?bool $isValidate = null;

    #[ORM\OneToMany(mappedBy: 'barbershop', targetEntity: BarbershopPics::class, orphanRemoval: true, cascade:['persist', 'remove'])]
    private Collection $barbershopPics;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options:["default"=> "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'user_barbershop_like')]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'barbershop', targetEntity: Avis::class)]
    private Collection $avis;

    #[ORM\OneToMany(mappedBy: 'barbershop', targetEntity: PrixPrestation::class)]
    private Collection $prixPrestations;

    public function __construct()
    {
        $this->barbershopPics = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->avis = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(string $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getHoraires(): ?string
    {
        return $this->horaires;
    }

    public function setHoraires(string $horaires): self
    {
        $this->horaires = $horaires;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): self
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function isIsValidate(): ?bool
    {
        return $this->isValidate;
    }

    public function setIsValidate(bool $isValidate): self
    {
        $this->isValidate = $isValidate;

        return $this;
    }

    /**
     * @return Collection<int, BarbershopPics>
     */
    public function getBarbershopPics(): Collection
    {
        return $this->barbershopPics;
    }

    public function addBarbershopPic(BarbershopPics $barbershopPic): self
    {
        if (!$this->barbershopPics->contains($barbershopPic)) {
            $this->barbershopPics->add($barbershopPic);
            $barbershopPic->setBarbershop($this);
        }

        return $this;
    }

    public function removeBarbershopPic(BarbershopPics $barbershopPic): self
    {
        if ($this->barbershopPics->removeElement($barbershopPic)) {
            // set the owning side to null (unless already changed)
            if ($barbershopPic->getBarbershop() === $this) {
                $barbershopPic->setBarbershop(null);
            }
        }

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getLikes(): Collection
    {
    return $this->likes;
    }

    public function addLike(User $like): self
    {
        if(!$this->likes->contains($like)) {
            $this->likes[] = $like;
        }

        return $this;
    }

    public function removeLike(User $like): self
    {
        $this->likes->removeElement($like);

        return $this;
    }

    public function isLikedByUser(User $user): bool
    {
        return $this->likes->contains($user);
    }

    public function howManyLikes(): int
    {
        return count($this->likes);
    }

    public function __toString()
    {
        return $this->nom;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): self
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setBarbershop($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): self
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getBarbershop() === $this) {
                $avi->setBarbershop(null);
            }
        }

        return $this;
    }

    public function getFullAdress(): string
    {
        return $this->adresse .' '. $this->cp .' '. $this->ville;
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
            $prixPrestation->setBarbershop($this);
        }

        return $this;
    }

    public function removePrixPrestation(PrixPrestation $prixPrestation): self
    {
        if ($this->prixPrestations->removeElement($prixPrestation)) {
            // set the owning side to null (unless already changed)
            if ($prixPrestation->getBarbershop() === $this) {
                $prixPrestation->setBarbershop(null);
            }
        }

        return $this;
    }

}
