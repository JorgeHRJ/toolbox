<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\IrrigationStatRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=IrrigationStatRepository::class)
 * @ORM\Table(name="irrigationstat")
 */
class IrrigationStat
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="irrigationstat_id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="irrigationstat_type", type="string", length=128, nullable=false)
     */
    private string $type;

    /**
     * @ORM\Column(name="irrigationstat_context", type="string", length=64, nullable=false)
     */
    private string $context;

    /**
     * @ORM\Column(name="irrigationstat_value", type="decimal", precision=10, scale=2, nullable=true)
     */
    private ?string $value;

    /**
     * @ORM\ManyToOne(targetEntity=IrrigationData::class, inversedBy="stats")
     * @ORM\JoinColumn(name="irrigationstat_data", referencedColumnName="irrigationdata_id", nullable=false)
     */
    private ?IrrigationData $data;

    /**
     * @ORM\Column(name="irrigationstat_created_at", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(name="irrigationstat_modified_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private \DateTimeInterface $modifiedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getData(): ?IrrigationData
    {
        return $this->data;
    }

    public function setData(?IrrigationData $data): self
    {
        $this->data = $data;

        return $this;
    }
}
