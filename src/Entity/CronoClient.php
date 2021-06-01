<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\CronoClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=CronoClientRepository::class)
 * @ORM\Table(name="cronoclient")
 */
class CronoClient
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="cronoclient_id", type="integer", nullable=false)
     */
    private int $id;

    /**
     * @ORM\Column(name="cronoclient_name", type="string", length=128, nullable=false)
     */
    private string $name;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="cronoclient_user", referencedColumnName="user_id", nullable=false)
     */
    private User $user;

    /**
     * @ORM\Column(name="cronoclient_color", type="string", length=16, nullable=false)
     */
    private string $color;

    /**
     * @ORM\OneToMany(targetEntity=CronoPlan::class, mappedBy="client", orphanRemoval=true)
     */
    private Collection $plans;

    /**
     * @ORM\OneToMany(targetEntity=CronoTime::class, mappedBy="client", orphanRemoval=true)
     */
    private Collection $times;

    /**
     * @ORM\Column(name="cronoclient_created_at", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(name="cronoclient_modified_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private \DateTimeInterface $modifiedAt;

    public function __construct()
    {
        $this->plans = new ArrayCollection();
        $this->times = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection|CronoPlan[]
     */
    public function getPlans(): Collection
    {
        return $this->plans;
    }

    public function addPlan(CronoPlan $plan): self
    {
        if (!$this->plans->contains($plan)) {
            $this->plans[] = $plan;
            $plan->setClient($this);
        }

        return $this;
    }

    public function removePlan(CronoPlan $plan): self
    {
        if ($this->plans->removeElement($plan)) {
            // set the owning side to null (unless already changed)
            if ($plan->getClient() === $this) {
                $plan->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CronoTime[]
     */
    public function getTimes(): Collection
    {
        return $this->times;
    }

    public function addTime(CronoTime $time): self
    {
        if (!$this->times->contains($time)) {
            $this->times[] = $time;
            $time->setClient($this);
        }

        return $this;
    }

    public function removeTime(CronoTime $time): self
    {
        if ($this->times->removeElement($time)) {
            // set the owning side to null (unless already changed)
            if ($time->getClient() === $this) {
                $time->setClient(null);
            }
        }

        return $this;
    }
}
