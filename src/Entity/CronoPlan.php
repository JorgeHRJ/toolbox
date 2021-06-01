<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\CronoPlanRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=CronoPlanRepository::class)
 * @ORM\Table(name="cronoplan")
 */
class CronoPlan
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="cronoplan_id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="cronoplan_expected", type="integer", nullable=false)
     */
    private int $expected;

    /**
     * @ORM\ManyToOne(targetEntity=CronoMonth::class, inversedBy="plans")
     * @ORM\JoinColumn(name="cronoplan_month", referencedColumnName="cronomonth_id", nullable=false)
     */
    private CronoMonth $month;

    /**
     * @ORM\ManyToOne(targetEntity=CronoClient::class, inversedBy="plans")
     * @ORM\JoinColumn(name="cronoplan_client", referencedColumnName="cronoclient_id", nullable=false)
     */
    private CronoClient $client;

    /**
     * @ORM\Column(name="cronoplan_created_at", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(name="cronoplan_modified_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private \DateTimeInterface $modifiedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExpected(): ?int
    {
        return $this->expected;
    }

    public function setExpected(int $expected): self
    {
        $this->expected = $expected;

        return $this;
    }

    public function getMonth(): ?CronoMonth
    {
        return $this->month;
    }

    public function setMonth(?CronoMonth $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getClient(): ?CronoClient
    {
        return $this->client;
    }

    public function setClient(?CronoClient $client): self
    {
        $this->client = $client;

        return $this;
    }
}
