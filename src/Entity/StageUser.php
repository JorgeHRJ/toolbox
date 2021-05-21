<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\StageUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=StageUserRepository::class)
 * @ORM\Table(name="stageuser")
 */
class StageUser
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="stageuser_id", type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Stage::class)
     * @ORM\JoinColumn(name="stageuser_stage", referencedColumnName="stage_id", nullable=false)
     */
    private Stage $stage;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="stageuser_user", referencedColumnName="user_id", nullable=false)
     */
    private User $user;

    /**
     * @ORM\Column(name="stageuser_comment", type="json", nullable=false)
     */
    private ?array $comment = [];

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="stageuser_created_at", type="datetime", nullable=false)
     */
    private \DateTimeInterface $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="stageuser_modified_at", type="datetime", nullable=true)
     */
    private \DateTimeInterface $modifiedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStage(): ?Stage
    {
        return $this->stage;
    }

    public function setStage(Stage $stage): self
    {
        $this->stage = $stage;

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

    public function getComment(): ?array
    {
        return $this->comment;
    }

    public function setComment(?array $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
