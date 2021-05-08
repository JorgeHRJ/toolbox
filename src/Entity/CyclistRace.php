<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\CyclistRaceRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=CyclistRaceRepository::class)
 * @ORM\Table(name="cyclist_race")
 */
class CyclistRace
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="cyclistrace_id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="cyclistrace_dorsal", type="integer", nullable=false)
     */
    private int $dorsal;

    /**
     * @ORM\ManyToOne(targetEntity=Cyclist::class)
     * @ORM\JoinColumn(name="cyclistrace_cyclist", referencedColumnName="cyclist_id", nullable=false)
     */
    private Cyclist $cyclist;

    /**
     * @ORM\ManyToOne(targetEntity=Race::class)
     * @ORM\JoinColumn(name="cyclistrace_race", referencedColumnName="race_id", nullable=false)
     */
    private Race $race;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="cyclistRaces")
     * @ORM\JoinColumn(name="cyclistrace_user", referencedColumnName="user_id", nullable=false)
     */
    private User $user;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="cyclistrace_created_at", type="datetime", nullable=false)
     */
    private \DateTime $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="cyclistrace_modified_at", type="datetime", nullable=true)
     */
    private \DateTime $modifiedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDorsal(): ?int
    {
        return $this->dorsal;
    }

    public function setDorsal(int $dorsal): self
    {
        $this->dorsal = $dorsal;

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

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): self
    {
        $this->race = $race;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
