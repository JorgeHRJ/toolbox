<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\CronoTimeRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CronoTimeRepository::class)
 * @ORM\Table(name="cronotime")
 */
class CronoTime
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="cronotime_id", type="integer", nullable=false)
     * @Groups({"detail"})
     */
    private int $id;

    /**
     * @ORM\Column(name="cronotime_start_at", type="datetime", nullable=false)
     * @Groups({"detail"})
     */
    private \DateTimeInterface $startAt;

    /**
     * @ORM\Column(name="cronotime_end_at", type="datetime", nullable=false)
     * @Groups({"detail"})
     */
    private \DateTimeInterface  $endAt;

    /**
     * @ORM\Column(name="cronotime_title", type="string", length=128, nullable=false)
     * @Groups({"detail"})
     */
    private string $title;

    /**
     * @ORM\Column(name="cronotime_description", type="text", nullable=true)
     * @Groups({"detail"})
     */
    private ?string $description = '';

    /**
     * @ORM\ManyToOne(targetEntity=CronoMonth::class, inversedBy="times")
     * @ORM\JoinColumn(name="cronotime_month", referencedColumnName="cronomonth_id", nullable=false)
     */
    private CronoMonth $month;

    /**
     * @ORM\ManyToOne(targetEntity=CronoClient::class, inversedBy="times")
     * @ORM\JoinColumn(name="cronotime_client", referencedColumnName="cronoclient_id", nullable=false)
     * @Groups({"detail"})
     */
    private ?CronoClient $client = null;

    /**
     * @ORM\Column(name="cronotime_created_at", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(name="cronotime_modified_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private \DateTimeInterface $modifiedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMonth(): ?CronoMonth
    {
        return $this->month;
    }

    public function setMonth(?CronoMonth $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getClient(): ?CronoClient
    {
        return $this->client;
    }

    public function setClient(?CronoClient $client): self
    {
        $this->client = $client;

        return $this;
    }
}
