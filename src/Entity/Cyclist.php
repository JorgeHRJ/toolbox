<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\CyclistRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CyclistRepository::class)
 * @ORM\Table(name="cyclist")
 */
class Cyclist
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="cyclist_id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="cyclist_name", type="string", length=128, nullable=false)
     */
    private string $name;

    /**
     * @Assert\DateTime()
     * @ORM\Column(name="cyclist_birthdate", type="datetime", nullable=false)
     */
    private \DateTime $birthDate;

    /**
     * @ORM\Column(name="cyclist_nationality", type="string", length=128, nullable=false)
     */
    private string $nationality;

    /**
     * @ORM\Column(name="cyclist_height", type="string", length=16, nullable=false)
     */
    private string $height;

    /**
     * @ORM\Column(name="cyclist_weight", type="string", length=16, nullable=false)
     */
    private string $weight;

    /**
     * @ORM\Column(name="cyclist_location", type="string", length=128, nullable=false)
     */
    private string $location;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="cyclist_created_at", type="datetime", nullable=false)
     */
    private \DateTime $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="cyclist_modified_at", type="datetime", nullable=true)
     */
    private \DateTime $modifiedAt;

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

    public function getBirthDate(): ?\DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTime $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(string $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(string $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }
}
