<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\GrandTourParticipationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=GrandTourParticipationRepository::class)
 * @ORM\Table(name="grandtour")
 */
class GrandTour
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="grandtour_id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="grandtour_season", type="string", length=4, nullable=false)
     */
    private string $season;

    /**
     * @ORM\Column(name="grandtour_name", type="string", length=128, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="grandtour_gc", type="string", length=8, nullable=false)
     */
    private string $gc;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="grandtour_created_at", type="datetime", nullable=false)
     */
    private \DateTime $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="grandtour_modified_at", type="datetime", nullable=true)
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

    public function getGc(): ?string
    {
        return $this->gc;
    }

    public function setGc(string $gc): self
    {
        $this->gc = $gc;

        return $this;
    }
}
