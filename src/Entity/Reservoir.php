<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\ReservoirRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ReservoirRepository::class)
 * @ORM\Table(name="reservoir")
 */
class Reservoir
{
    use TimestampableTrait;

    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="reservoir_id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reservoir_name", type="string", length=32, nullable=false)
     */
    private $name;

    /**
     * @var int|null
     *
     * @ORM\Column(name="reservoir_capacity", type="integer", nullable=false)
     */
    private $capacity;

    /**
     * @var ReservoirMunicipality|null
     *
     * @ORM\ManyToOne(targetEntity=ReservoirMunicipality::class, inversedBy="reservoirs")
     * @ORM\JoinColumn(name="reservoir_municipality", referencedColumnName="reservoirmunicipality_id", nullable=false)
     */
    private $municipality;

    /**
     * @var Collection|null
     *
     * @ORM\OneToMany(targetEntity=ReservoirData::class, mappedBy="reservoir")
     */
    private $data;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="reservoir_created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="reservoir_modified_at", type="datetime", nullable=true)
     */
    private $modifiedAt;

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

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getMunicipality(): ?ReservoirMunicipality
    {
        return $this->municipality;
    }

    public function setMunicipality(?ReservoirMunicipality $municipality): self
    {
        $this->municipality = $municipality;

        return $this;
    }

    /**
     * @return Collection|ReservoirData[]
     */
    public function getData(): Collection
    {
        return $this->data;
    }

    public function addReservoirData(ReservoirData $data): self
    {
        if (!$this->data->contains($data)) {
            $this->$data[] = $data;
            $data->setReservoir($this);
        }

        return $this;
    }

    public function removeReservoirData(ReservoirData $data): self
    {
        if ($this->data->removeElement($data)) {
            // set the owning side to null (unless already changed)
            if ($data->getReservoir() === $this) {
                $data->setReservoir(null);
            }
        }

        return $this;
    }
}
