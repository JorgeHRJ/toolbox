<?php

namespace App\Entity;

use App\Repository\StageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=StageRepository::class)
 * @ORM\Table("stage")
 */
class Stage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="stage_id", type="integer", nullable=false)
     */
    private int $id;

    /**
     * @ORM\Column(name="stage_number", type="integer", nullable=false)
     */
    private int $number;

    /**
     * @ORM\Column(name="stage_date", type="datetime", nullable=true)
     */
    private \DateTime $date;

    /**
     * @ORM\Column(name="stage_distance", type="string", length=32, nullable=true)
     */
    private ?string $distance;

    /**
     * @ORM\Column(name="stage_vertical", type="string", length=32, nullable=true)
     */
    private ?string $vertical;

    /**
     * @ORM\Column(name="stage_departure", type="string", length=128, nullable=true)
     */
    private ?string $departure;

    /**
     * @ORM\Column(name="stage_arrival", type="string", length=128, nullable=true)
     */
    private ?string $arrival;

    /**
     * @ORM\Column(name="stage_type", type="string", length=32, nullable=true)
     */
    private ?string $type;

    /**
     * @ORM\ManyToOne(targetEntity=Race::class, inversedBy="stages")
     * @ORM\JoinColumn(name="stage_race", referencedColumnName="race_id", nullable=false)
     */
    private ?Race $race;

    /**
     * @ORM\OneToMany(targetEntity=StageAsset::class, mappedBy="stage", orphanRemoval=true)
     */
    private Collection $assets;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="stage_created_at", type="datetime", nullable=false)
     */
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->assets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDistance(): ?string
    {
        return $this->distance;
    }

    public function setDistance(?string $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getVertical(): ?string
    {
        return $this->vertical;
    }

    public function setVertical(?string $vertical): self
    {
        $this->vertical = $vertical;

        return $this;
    }

    public function getDeparture(): ?string
    {
        return $this->departure;
    }

    public function setDeparture(?string $departure): self
    {
        $this->departure = $departure;

        return $this;
    }

    public function getArrival(): ?string
    {
        return $this->arrival;
    }

    public function setArrival(?string $arrival): self
    {
        $this->arrival = $arrival;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): self
    {
        $this->race = $race;

        return $this;
    }

    /**
     * @return Collection|StageAsset[]
     */
    public function getAssets(): Collection
    {
        return $this->assets;
    }

    public function addAsset(StageAsset $asset): self
    {
        if (!$this->assets->contains($asset)) {
            $this->assets[] = $asset;
            $asset->setStage($this);
        }

        return $this;
    }

    public function removeAsset(StageAsset $asset): self
    {
        if ($this->assets->removeElement($asset)) {
            // set the owning side to null (unless already changed)
            if ($asset->getStage() === $this) {
                $asset->setStage(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
