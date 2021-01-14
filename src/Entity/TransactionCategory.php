<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\TransactionCategoryRepository;
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
     * @Assert\Choice(choices="self::PERIDIOCITIES")
     *
     * @ORM\Column(name="transactioncategory_peridiocity", type="integer", nullable=false)
     */
    private $periodicity;

    /**
     * @var int|null
     *
     * @Assert\Choice(choices="self::TYPES")
     *
     * @ORM\Column(name="transactioncategory_type", type="integer", nullable=false)
     */
    private $type;

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

    public function setModifiedAt(?\DateTimeInterface $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }
}
