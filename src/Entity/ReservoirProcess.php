<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\ReservoirProcessRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ReservoirProcessRepository::class)
 * @ORM\Table(name="reservoirprocess")
 */
class ReservoirProcess
{
    use TimestampableTrait;

    const PENDING_STATUS = 0;
    const DONE_STATUS = 1;
    const ERROR_STATUS = 9;

    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="reservoirprocess_id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reservoirprocess_filename", type="string", length=255, nullable=false)
     */
    private $filename;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(name="reservoirprocess_date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var int|null
     *
     * @ORM\Column(name="reservoirprocess_status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var Collection|null
     *
     * @ORM\OneToMany(targetEntity=ReservoirData::class, mappedBy="process")
     */
    private $data;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="reservoirprocess_created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="reservoirprocess_modified_at", type="datetime", nullable=true)
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

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|ReservoirData[]
     */
    public function getData(): Collection
    {
        return $this->data;
    }

    public function addData(ReservoirData $data): self
    {
        if (!$this->data->contains($data)) {
            $this->data[] = $data;
            $data->setProcess($this);
        }

        return $this;
    }

    public function removeData(ReservoirData $data): self
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
