<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\ClassicRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ClassicRepository::class)
 * @ORM\Table(name="classic")
 */
class Classic
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="classic_id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="classic_season", type="string", length=4, nullable=false)
     */
    private string $season;

    /**
     * @ORM\Column(name="classic_name", type="string", length=128, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="classic_result", type="string", length=16, nullable=false)
     */
    private string $result;

    /**
     * @ORM\ManyToOne(targetEntity=Cyclist::class, inversedBy="classics")
     * @ORM\JoinColumn(name="classic_cyclist", referencedColumnName="cyclist_id", nullable=false)
     */
    private Cyclist $cyclist;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="classic_created_at", type="datetime", nullable=false)
     */
    private \DateTime $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="classic_modified_at", type="datetime", nullable=true)
     */
    private \DateTime $modifiedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeason(): ?string
    {
        return $this->season;
    }

    public function setSeason(string $season): self
    {
        $this->season = $season;

        return $this;
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

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(string $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getCyclist(): ?Cyclist
    {
        return $this->cyclist;
    }

    public function setCyclist(?Cyclist $cyclist): self
    {
        $this->cyclist = $cyclist;

        return $this;
    }
}
