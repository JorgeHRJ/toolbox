<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 * @ORM\Table(name="notification")
 */
class Notification
{
    const RESERVOIR_PROCESSED = 'reservoir_processed';
    const IRRIGATION_PROCESSED = 'irrigation_processed';

    const UNREAD_STATUS = 0;
    const READ_STATUS = 1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="notification_id", type="integer", nullable=false)
     */
    private int $id;

    /**
     * @ORM\Column(name="notification_type", type="string", length=128, nullable=false)
     */
    private string $type;

    /**
     * @ORM\Column(name="notification_content", type="text", nullable=false)
     */
    private string $content;

    /**
     * @ORM\Column(name="notification_status", type="integer", nullable=false)
     */
    private int $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="notifications")
     * @ORM\JoinColumn(name="notification_user", referencedColumnName="user_id", nullable=false)
     */
    private ?User $user;

    /**
     * @ORM\Column(name="notification_created_at", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    private \DateTimeInterface $createdAt;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
