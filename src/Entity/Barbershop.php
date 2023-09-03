<?php

namespace App\Entity;

use App\Entity\User;
use Cocur\Slugify\Slugify;
use ApiPlatform\Metadata\Get;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\BarbershopRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource]
#[ORM\Entity(repositoryClass: BarbershopRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Barbershop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide. ")]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide. ")]
    private ?string $description = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: "L'adresse ne peut pas être vide. ")]
    private ?string $adresse = null;

    #[ORM\Column(length: 15)]
    #[Assert\NotBlank(message: "Le code postal ne peut pas être vide. ")]
    private ?string $cp = null;

    #[ORM\Column(length: 45)]
    #[Assert\NotBlank(message: "La ville ne peut pas être vide. ")]
    private ?string $ville = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $horaires = null;

    #[ORM\Column]
    private ?float $latitude = null;

    #[ORM\Column]
    private ?float $longitude = null;

    #[ORM\Column(length: 16)]
    #[Assert\NotBlank(message: "La téléphone ne peut pas être vide. ")]
    private ?string $telephone = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $instagram = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $facebook = null;

    #[Ignore]
    #[ORM\Column (type: 'boolean', nullable: true, options: ['default' => false])]
    private ?bool $validate = null;

    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'barbershop', targetEntity: BarbershopPics::class, orphanRemoval: true, cascade:['persist', 'remove'])]
    private Collection $barbershopPics;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options:["default"=> "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $creationDate = null;

    #[Ignore]
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'user_barbershop_like')]
    private Collection $likes;

    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'barbershop', targetEntity: Avis::class)]
    private Collection $avis;

    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'barbershop', targetEntity: BarberPrestation::class)]
    private Collection $barberPrestations;

    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'barbershop', targetEntity: Personnel::class)]
    private Collection $personnels;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    public function __construct()
    {
        $this->barbershopPics = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->barberPrestations = new ArrayCollection();
        $this->personnels = new ArrayCollection();
    }

    // Générer le slug à chaque ajout de barbier
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

    public function isValidate(): ?bool
    {
        return $this->validate;
    }

    public function setValidate(bool $validate): self
    {
        $this->validate = $validate;

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
            $barberPrestation->setBarbershop($this);
        }

        return $this;
    }

    public function removeBarberPrestation(BarberPrestation $barberPrestation): self
    {
        if ($this->barberPrestations->removeElement($barberPrestation)) {
            // set the owning side to null (unless already changed)
            if ($barberPrestation->getBarbershop() === $this) {
                $barberPrestation->setBarbershop(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Personnel>
     */
    public function getPersonnels(): Collection
    {
        return $this->personnels;
    }

    public function addPersonnel(Personnel $personnel): self
    {
        if (!$this->personnels->contains($personnel)) {
            $this->personnels->add($personnel);
            $personnel->setBarbershop($this);
        }

        return $this;
    }

    public function removePersonnel(Personnel $personnel): self
    {
        if ($this->personnels->removeElement($personnel)) {
            // set the owning side to null (unless already changed)
            if ($personnel->getBarbershop() === $this) {
                $personnel->setBarbershop(null);
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
