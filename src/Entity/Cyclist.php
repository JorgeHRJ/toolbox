<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\CyclistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CyclistRepository::class)
 * @ORM\Table(name="cyclist")
 * @UniqueEntity("slug")
 */
class Cyclist
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="cyclist_id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="cyclist_name", type="string", length=128, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="cyclist_slug", type="string", length=255, unique=true, nullable=false)
     */
    private string $slug;

    /**
     * @Assert\DateTime()
     * @ORM\Column(name="cyclist_birthdate", type="datetime", nullable=true)
     */
    private ?\DateTime $birthDate;

    /**
     * @ORM\Column(name="cyclist_nationality", type="string", length=128, nullable=true)
     */
    private ?string $nationality;

    /**
     * @ORM\Column(name="cyclist_height", type="string", length=16, nullable=true)
     */
    private ?string $height;

    /**
     * @ORM\Column(name="cyclist_weight", type="string", length=16, nullable=true)
     */
    private ?string $weight;

    /**
     * @ORM\Column(name="cyclist_location", type="string", length=128, nullable=true)
     */
    private ?string $location;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="cyclists")
     * @ORM\JoinColumn(name="cyclist_team", referencedColumnName="team_id", nullable=false)
     */
    private ?Team $team;

    /**
     * @ORM\OneToMany(targetEntity=Win::class, mappedBy="cyclist", orphanRemoval=true)
     * @ORM\OrderBy({"date" = "DESC"})
     */
    private Collection $wins;

    /**
     * @ORM\OneToMany(targetEntity=GrandTour::class, mappedBy="cyclist", orphanRemoval=true)
     * @ORM\OrderBy({"season" = "DESC"})
     */
    private Collection $grandTours;

    /**
     * @ORM\OneToMany(targetEntity=Classic::class, mappedBy="cyclist", orphanRemoval=true)
     * @ORM\OrderBy({"season" = "DESC"})
     */
    private Collection $classics;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="cyclist_created_at", type="datetime", nullable=false)
     */
    private \DateTimeInterface $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="cyclist_modified_at", type="datetime", nullable=true)
     */
    private \DateTimeInterface $modifiedAt;

    public function __construct()
    {
        $this->wins = new ArrayCollection();
        $this->grandTours = new ArrayCollection();
        $this->classics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getBirthDate(): ?\DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTime $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(?string $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(?string $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    /**
     * @return Collection|Win[]
     */
    public function getWins(): Collection
    {
        return $this->wins;
    }

    public function addWin(Win $win): self
    {
        if (!$this->wins->contains($win)) {
            $this->wins[] = $win;
            $win->setCyclist($this);
        }

        return $this;
    }

    public function removeWin(Win $win): self
    {
        if ($this->wins->removeElement($win)) {
            // set the owning side to null (unless already changed)
            if ($win->getCyclist() === $this) {
                $win->setCyclist(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GrandTour[]
     */
    public function getGrandTours(): Collection
    {
        return $this->grandTours;
    }

    public function addGrandTour(GrandTour $grandTour): self
    {
        if (!$this->grandTours->contains($grandTour)) {
            $this->grandTours[] = $grandTour;
            $grandTour->setCyclist($this);
        }

        return $this;
    }

    public function removeGrandTour(GrandTour $grandTour): self
    {
        if ($this->grandTours->removeElement($grandTour)) {
            // set the owning side to null (unless already changed)
            if ($grandTour->getCyclist() === $this) {
                $grandTour->setCyclist(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Classic[]
     */
    public function getClassics(): Collection
    {
        return $this->classics;
    }

    public function addClassic(Classic $classic): self
    {
        if (!$this->classics->contains($classic)) {
            $this->classics[] = $classic;
            $classic->setCyclist($this);
        }

        return $this;
    }

    public function removeClassic(Classic $classic): self
    {
        if ($this->classics->removeElement($classic)) {
            // set the owning side to null (unless already changed)
            if ($classic->getCyclist() === $this) {
                $classic->setCyclist(null);
            }
        }

        return $this;
    }
}
