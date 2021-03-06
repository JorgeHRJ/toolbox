<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Ya existe un usuario con este email"
 * )
 */
class User implements UserInterface
{
    use TimestampableTrait;

    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER = 'ROLE_USER';
    const ROLE_TASK = 'ROLE_TASK';
    const ROLE_TRANSACTION = 'ROLE_TRANSACTION';
    const ROLE_RESERVOIR = 'ROLE_RESERVOIR';
    const ROLE_RACEBOOK = 'ROLE_RACEBOOK';
    const ROLE_IRRIGATION = 'ROLE_IRRIGATION';
    const ROLE_CRONOS = 'ROLE_CRONOS';
    const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_TASK,
        self::ROLE_TRANSACTION,
        self::ROLE_RESERVOIR,
        self::ROLE_RACEBOOK,
        self::ROLE_IRRIGATION,
        self::ROLE_CRONOS,
        self::ROLE_USER
    ];

    const DISABLED_STATUS = 0;
    const ENABLED_STATUS = 1;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private int $id;

    /**
     * @ORM\Column(name="user_email", type="string", length=180, unique=true, nullable=false)
     */
    private string $email;

    /**
     * @ORM\Column(name="user_roles", type="json", nullable=false)
     */
    private array $roles = [];

    /**
     * @ORM\Column(name="user_password", type="string", nullable=false)
     */
    private string $password;

    /**
     * @ORM\Column(name="user_status", type="smallint", nullable=false)
     */
    private ?int $status;

    /**
     * @ORM\Column(name="user_reportable", type="boolean", nullable=false)
     */
    private bool $reportable;

    /**
     * @ORM\OneToMany(targetEntity=CyclistRace::class, mappedBy="user")
     */
    private Collection $cyclistRaces;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="user", orphanRemoval=true)
     */
    private Collection $notifications;

    /**
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="user_created_at", type="datetime", nullable=false)
     */
    private \DateTimeInterface $createdAt;

    /**
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="user_modified_at", type="datetime", nullable=true)
     */
    private \DateTimeInterface $modifiedAt;

    public function __construct()
    {
        $this->cyclistRaces = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        if (!in_array(self::ROLE_USER, $roles)) {
            $roles[] = self::ROLE_USER;
        }

        return $roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     */
    public function setStatus(?int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function getReportable(): bool
    {
        return $this->reportable;
    }

    /**
     * @param bool $reportable
     * @return $this
     */
    public function setReportable(bool $reportable): self
    {
        $this->reportable = $reportable;

        return $this;
    }

    /**
     * @return Collection|CyclistRace[]
     */
    public function getCyclistRaces(): Collection
    {
        return $this->cyclistRaces;
    }

    public function addCyclistRace(CyclistRace $cyclistRace): self
    {
        if (!$this->cyclistRaces->contains($cyclistRace)) {
            $this->cyclistRaces[] = $cyclistRace;
            $cyclistRace->setUser($this);
        }

        return $this;
    }

    public function removeCyclistRace(CyclistRace $cyclistRace): self
    {
        if ($this->cyclistRaces->removeElement($cyclistRace)) {
            // set the owning side to null (unless already changed)
            if ($cyclistRace->getUser() === $this) {
                $cyclistRace->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }
}
