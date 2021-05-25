<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\IrrigationZoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=IrrigationZoneRepository::class)
 * @ORM\Table(name="irrigationzone")
 */
class IrrigationZone
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="irrigationzone_id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="irrigationzone_name", type="string", length=255, nullable=false)
     */
    private string $name;

    /**
     * @ORM\OneToMany(targetEntity=IrrigationData::class, mappedBy="zone")
     */
    private Collection $data;

    /**
     * @ORM\Column(name="irrigationzone_created_at", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(name="irrigationzone_modified_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private \DateTimeInterface $modifiedAt;

    public function __construct()
    {
        $this->data = new ArrayCollection();
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

    /**
     * @return Collection|IrrigationData[]
     */
    public function getData(): Collection
    {
        return $this->data;
    }

    public function addData(IrrigationData $data): self
    {
        if (!$this->data->contains($data)) {
            $this->data[] = $data;
            $data->setZone($this);
        }

        return $this;
    }

    public function removeData(IrrigationData $data): self
    {
        if ($this->data->removeElement($data)) {
            // set the owning side to null (unless already changed)
            if ($data->getZone() === $this) {
                $data->setZone(null);
            }
        }

        return $this;
    }
}
