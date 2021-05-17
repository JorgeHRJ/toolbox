<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\RaceRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=RaceRepository::class)
 * @ORM\Table(name="race")
 * @UniqueEntity("slug")
 */
class Race
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="race_id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="race_name", type="string", length=128, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="race_slug", type="string", length=255, unique=true, nullable=false)
     */
    private string $slug;

    /**
     * @ORM\Column(name="race_startlist_url", type="string", length=255, nullable=false)
     */
    private string $startlistUrl;

    /**
     * @ORM\Column(name="race_year", type="string", length=4, nullable=false)
     */
    private string $year;

    /**
     * @ORM\Column(name="race_start_date", type="datetime", nullable=false)
     */
    private \DateTimeInterface $startDate;

    /**
     * @ORM\Column(name="race_end_date", type="datetime", nullable=false)
     */
    private \DateTimeInterface $endDate;

    /**
     * @ORM\Column(name="race_category", type="string", length=64, nullable=false)
     */
    private string $category;

    /**
     * @ORM\Column(name="race_uci_tour", type="string", length=64, nullable=false)
     */
    private string $uciTour;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="race_created_at", type="datetime", nullable=false)
     */
    private \DateTimeInterface $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="race_modified_at", type="datetime", nullable=true)
     */
    private \DateTimeInterface $modifiedAt;

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

    public function getStartlistUrl(): ?string
    {
        return $this->startlistUrl;
    }

    public function setStartlistUrl(string $startlistUrl): self
    {
        $this->startlistUrl = $startlistUrl;

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getUciTour(): ?string
    {
        return $this->uciTour;
    }

    public function setUciTour(string $uciTour): self
    {
        $this->uciTour = $uciTour;

        return $this;
    }
}
