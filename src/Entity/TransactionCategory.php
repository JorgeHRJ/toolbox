<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\TransactionCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TransactionCategoryRepository::class)
 * @ORM\Table(name="transactioncategory")
 */
class TransactionCategory
{
    use TimestampableTrait;

    const NO_PERIDIOCITY = 0;
    const MONTHLY_PERIDIOCITY = 1;
    const PERIDIOCITIES = [self::NO_PERIDIOCITY, self::MONTHLY_PERIDIOCITY];

    const EXPENSE_TYPE = 0;
    const INCOME_TYPE = 1;
    const TYPES = [self::EXPENSE_TYPE, self::INCOME_TYPE];

    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="transactioncategory_id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="El título no puede estar vacío")
     * @Assert\Length(max=128, maxMessage="El título no puede superar {{ limit }} caracteres")
     *
     * @ORM\Column(name="transactioncategory_title", type="string", length=128, nullable=false)
     */
    private $title;

    /**
     * @var int|null
     *
     * @Assert\Choice(choices=TransactionCategory::PERIDIOCITIES, message="Elige una peridiocidad válida")
     *
     * @ORM\Column(name="transactioncategory_peridiocity", type="integer", nullable=false)
     */
    private $periodicity;

    /**
     * @var int|null
     *
     * @Assert\Choice(choices=TransactionCategory::TYPES, message="Elige una peridiocidad válida")
     *
     * @ORM\Column(name="transactioncategory_type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="transactioncategory_user", referencedColumnName="user_id", nullable=false)
     */
    private $user;

    /**
     * @var Collection|null
     *
     * @ORM\OneToMany(targetEntity=TransactionMonth::class, mappedBy="category", cascade={"persist", "remove"})
     */
    private $months;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="transactioncategory_created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="transactioncategory_modified_at", type="datetime", nullable=true)
     */
    private $modifiedAt;

    public function __construct()
    {
        $this->months = new ArrayCollection();
    }

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

    public function getPeriodicity(): ?int
    {
        return $this->periodicity;
    }

    public function setPeriodicity(int $periodicity): self
    {
        $this->periodicity = $periodicity;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

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

    /**
     * @return Collection|TransactionMonth[]
     */
    public function getMonths(): Collection
    {
        return $this->months;
    }

    /**
     * @param TransactionMonth $month
     * @return $this
     */
    public function addMonth(TransactionMonth $month): self
    {
        if (!$this->months->contains($month)) {
            $this->months[] = $month;
            $month->setCategory($this);
        }

        return $this;
    }

    /**
     * @param TransactionMonth $month
     * @return $this
     */
    public function removeMonth(TransactionMonth $month): self
    {
        if ($this->months->contains($month)) {
            $this->months->removeElement($month);
            // set the owning side to null (unless already changed)
            if ($month->getCategory() === $this) {
                $month->setCategory(null);
            }
        }

        return $this;
    }
}
