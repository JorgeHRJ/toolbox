<?php

namespace App\Entity;

use App\Repository\IrrigationProcessRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=IrrigationProcessRepository::class)
 * @ORM\Table(name="irrigationprocess")
 */
class IrrigationProcess
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="irrigationprocess_id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="irrigationprocess_date", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    private \DateTimeInterface $date;

    /**
     * @ORM\Column(name="irrigationprocess_errors", type="json", nullable=false)
     */
    private array $errors = [];

    /**
     * @ORM\OneToMany(targetEntity=IrrigationData::class, mappedBy="proccess", orphanRemoval=true)
     */
    private Collection $data;

    public function __construct()
    {
        $this->data = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): self
    {
        $this->errors = $errors;

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
            $data->setProcess($this);
        }

        return $this;
    }

    public function removeData(IrrigationData $data): self
    {
        if ($this->data->removeElement($data)) {
            // set the owning side to null (unless already changed)
            if ($data->getProcess() === $this) {
                $data->setProcess(null);
            }
        }

        return $this;
    }
}
