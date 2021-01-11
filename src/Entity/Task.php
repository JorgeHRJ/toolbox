<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    use TimestampableTrait;

    const PENDING_STATUS = 0;
    const DONE_STATUS = 1;
    const STATUSES = [self::PENDING_STATUS, self::DONE_STATUS];

    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="task_id", type="integer", nullable=false)
     *
     * @Groups({"show"})
     */
    private $id;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="El título no puede estar vacío")
     * @Assert\Length(max=128, maxMessage="El título no puede superar {{ limit }} caracteres")
     *
     * @ORM\Column(name="task_title", type="string", length=128, nullable=false)
     *
     * @Groups({"show"})
     */
    private $title;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     *
     * @ORM\Column(name="task_date", type="datetime", nullable=false)
     *
     * @Groups({"show"})
     */
    private $date;

    /**
     * @var int|null
     *
     * @Assert\Choice(choices=self::STATUSES, message="Valor inválido para el estado de la tarea")
     *
     * @ORM\Column(name="task_status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="task_user", referencedColumnName="user_id", nullable=false)
     */
    private $user;

    /**
     * @var TaskTag|null
     *
     * @ORM\ManyToOne(targetEntity=TaskTag::class, cascade={"persist"})
     * @ORM\JoinColumn(name="task_tag", referencedColumnName="tasktag_id", nullable=false)
     *
     * @Groups({"show"})
     */
    private $tag;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="task_created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="task_modified_at", type="datetime", nullable=true)
     */
    private $modifiedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getModifiedAt(): ?\DateTimeInterface
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(\DateTimeInterface $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getTag(): ?TaskTag
    {
        return $this->tag;
    }

    public function setTag(?TaskTag $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
}
