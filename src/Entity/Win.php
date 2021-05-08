<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\WinRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=WinRepository::class)
 * @ORM\Table(name="win")
 */
class Win
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="win_id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="win_type", type="string", length=16, nullable=false)
     */
    private string $type;

    /**
     * @ORM\Column(name="win_race", type="string", length=128, nullable=false)
     */
    private string $race;

    /**
     * @ORM\Column(name="win_class", type="string", length=36, nullable=false)
     */
    private string $class;

    /**
     * @ORM\Column(name="win_date", type="datetime", nullable=false)
     */
    private \DateTime $date;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="win_created_at", type="datetime", nullable=false)
     */
    private \DateTime $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="win_modified_at", type="datetime", nullable=true)
     */
    private \DateTime $modifiedAt;

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

    public function getRace(): ?string
    {
        return $this->race;
    }

    public function setRace(string $race): self
    {
        $this->race = $race;

        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }
}
