<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\CronoMonthRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=CronoMonthRepository::class)
 * @ORM\Table(name="cronomonth")
 */
class CronoMonth
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="cronomonth_id", type="integer", nullable=false)
     */
    private int $id;

    /**
     * @ORM\Column(name="cronomonth_month", type="integer", nullable=false)
     */
    private int $month;

    /**
     * @ORM\Column(name="cronomonth_year", type="integer", nullable=false)
     */
    private int $year;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="cronomonth_user", referencedColumnName="user_id", nullable=false)
     */
    private User $user;

    /**
     * @ORM\OneToMany(targetEntity=CronoPlan::class, mappedBy="month", orphanRemoval=true)
     */
    private Collection $plans;

    /**
     * @ORM\OneToMany(targetEntity=CronoTime::class, mappedBy="month", orphanRemoval=true)
     */
    private Collection $times;

    /**
     * @ORM\Column(name="cronomonth_created_at", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(name="cronomonth_modified_at", type="datetime", nullable=true)
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

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(int $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

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
            $plan->setMonth($this);
        }

        return $this;
    }

    public function removePlan(CronoPlan $plan): self
    {
        if ($this->plans->removeElement($plan)) {
            // set the owning side to null (unless already changed)
            if ($plan->getMonth() === $this) {
                $plan->setMonth(null);
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
            $time->setMonth($this);
        }

        return $this;
    }

    public function removeTime(CronoTime $time): self
    {
        if ($this->times->removeElement($time)) {
            // set the owning side to null (unless already changed)
            if ($time->getMonth() === $this) {
                $time->setMonth(null);
            }
        }

        return $this;
    }
}
