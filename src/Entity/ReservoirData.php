<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\ReservoirDataRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ReservoirDataRepository::class)
 * @ORM\Table(name="reservoirdata")
 */
class ReservoirData
{
    use TimestampableTrait;

    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="reservoirdata_id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="reservoirdata_fillness", type="integer", nullable=false)
     */
    private $fillness;

    /**
     * @var Reservoir|null
     *
     * @ORM\ManyToOne(targetEntity=Reservoir::class, inversedBy="data")
     * @ORM\JoinColumn(name="reservoirdata_reservoir", referencedColumnName="reservoir_id", nullable=false)
     */
    private $reservoir;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="reservoirdata_created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="reservoirdata_modified_at", type="datetime", nullable=true)
     */
    private $modifiedAt;

    /**
     * @var ReservoirProcess|null
     *
     * @ORM\ManyToOne(targetEntity=ReservoirProcess::class, inversedBy="data")
     * @ORM\JoinColumn(name="reservoirdata_process", referencedColumnName="reservoirprocess_id", nullable=false)
     */
    private $process;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFillness(): ?int
    {
        return $this->fillness;
    }

    public function setFillness(int $fillness): self
    {
        $this->fillness = $fillness;

        return $this;
    }

    public function getReservoir(): ?Reservoir
    {
        return $this->reservoir;
    }

    public function setReservoir(?Reservoir $reservoir): self
    {
        $this->reservoir = $reservoir;

        return $this;
    }

    public function getProcess(): ?ReservoirProcess
    {
        return $this->process;
    }

    public function setProcess(?ReservoirProcess $process): self
    {
        $this->process = $process;

        return $this;
    }
}
