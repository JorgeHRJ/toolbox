<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\IrrigationDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=IrrigationDataRepository::class)
 * @ORM\Table(name="irrigationdata")
 */
class IrrigationData
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="irrigationdata_id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="irrigationdata_filename", type="string", length=255, nullable=false)
     */
    private string $filename;

    /**
     * @ORM\Column(name="irrigationdata_url", type="string", length=255, nullable=false)
     */
    private string $url;

    /**
     * @ORM\Column(name="irrigationdata_start_date", type="datetime", nullable=false)
     */
    private \DateTimeInterface $startDate;

    /**
     * @ORM\Column(name="irrigationdata_end_date", type="datetime", nullable=false)
     */
    private \DateTimeInterface $endDate;

    /**
     * @ORM\ManyToOne(targetEntity=IrrigationProcess::class, inversedBy="data")
     * @ORM\JoinColumn(name="irrigationdata_process", referencedColumnName="irrigationprocess_id", nullable=false)
     */
    private ?IrrigationProcess $process;

    /**
     * @ORM\ManyToOne(targetEntity=IrrigationZone::class, inversedBy="data")
     * @ORM\JoinColumn(name="irrigationdata_zone", referencedColumnName="irrigationzone_id", nullable=false)
     */
    private ?IrrigationZone $zone;

    /**
     * @ORM\OneToMany(targetEntity=IrrigationStat::class, mappedBy="data", orphanRemoval=true)
     */
    private Collection $stats;

    /**
     * @ORM\Column(name="irrigationdata_created_at", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(name="irrigationdata_modified_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private \DateTimeInterface $modifiedAt;

    public function __construct()
    {
        $this->stats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

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

    public function getProcess(): ?IrrigationProcess
    {
        return $this->process;
    }

    public function setProcess(?IrrigationProcess $process): self
    {
        $this->process = $process;

        return $this;
    }

    public function getZone(): ?IrrigationZone
    {
        return $this->zone;
    }

    public function setZone(?IrrigationZone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * @return Collection|IrrigationStat[]
     */
    public function getStats(): Collection
    {
        return $this->stats;
    }

    public function addStat(IrrigationStat $stat): self
    {
        if (!$this->stats->contains($stat)) {
            $this->stats[] = $stat;
            $stat->setData($this);
        }

        return $this;
    }

    public function removeStat(IrrigationStat $stat): self
    {
        if ($this->stats->removeElement($stat)) {
            // set the owning side to null (unless already changed)
            if ($stat->getData() === $this) {
                $stat->setData(null);
            }
        }

        return $this;
    }
}
