<?php

namespace App\Entity;

use App\Repository\TaskTagRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TaskTagRepository::class)
 * @ORM\Table(name="tasktag")
 */
class TaskTag
{
    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="tasktag_id", type="integer", nullable=false)
     *
     * @Groups({"show"})
     */
    private $id;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="El nombre no puede estar vacío")
     * @Assert\Length(max=128, maxMessage="El nombre no puede superar {{ limit }} caracteres")
     *
     * @ORM\Column(name="tasktag_name", type="string", length=128, nullable=false)
     *
     * @Groups({"show"})
     */
    private $name;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="El color no puede estar vacío")
     * @Assert\Length(max=16, maxMessage="El color no puede superar {{ limit }} caracteres")
     *
     * @ORM\Column(name="tasktag_color", type="string", length=16, nullable=false)
     *
     * @Groups({"show"})
     */
    private $color;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="tasktag_user", referencedColumnName="user_id", nullable=false)
     */
    private $user;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="tasktag_created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="tasktag_modified_at", type="datetime", nullable=true)
     */
    private $modifiedAt;

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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

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
}
