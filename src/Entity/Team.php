<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=TeamRepository::class)
 * @ORM\Table(name="team")
 * @UniqueEntity("slug")
 */
class Team
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="team_id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="team_name", type="string", length=128, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="team_slug", type="string", length=255, unique=true, nullable=false)
     */
    private string $slug;

    /**
     * @ORM\OneToMany(targetEntity=Cyclist::class, mappedBy="team")
     */
    private Collection $cyclists;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="team_created_at", type="datetime", nullable=false)
     */
    private \DateTimeInterface $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="team_modified_at", type="datetime", nullable=true)
     */
    private \DateTimeInterface $modifiedAt;

    public function __construct()
    {
        $this->cyclists = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Cyclist[]
     */
    public function getCyclists(): Collection
    {
        return $this->cyclists;
    }

    public function addCyclist(Cyclist $cyclist): self
    {
        if (!$this->cyclists->contains($cyclist)) {
            $this->cyclists[] = $cyclist;
            $cyclist->setTeam($this);
        }

        return $this;
    }

    public function removeCyclist(Cyclist $cyclist): self
    {
        if ($this->cyclists->removeElement($cyclist)) {
            // set the owning side to null (unless already changed)
            if ($cyclist->getTeam() === $this) {
                $cyclist->setTeam(null);
            }
        }

        return $this;
    }
}
