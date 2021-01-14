<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\TransactionMonthRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TransactionMonthRepository::class)
 * @ORM\Table(name="transactionmonth")
 */
class TransactionMonth
{
    use TimestampableTrait;

    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="transactionmonth_id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="transactionmonth_month", type="integer", nullable=false)
     */
    private $month;

    /**
     * @var int|null
     *
     * @ORM\Column(name="transactionmonth_year", type="integer", nullable=false)
     */
    private $year;

    /**
     * @var string|null
     *
     * @ORM\Column(name="transactionmonth_expected", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $expected;

    /**
     * @var string|null
     *
     * @ORM\Column(name="transactionmonth_value", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $value;

    /**
     * @var TransactionCategory|null
     *
     * @ORM\ManyToOne(targetEntity=TransactionCategory::class)
     * @ORM\JoinColumn(name="transactionmonth_category", referencedColumnName="transactioncategory_id", nullable=false)
     */
    private $category;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="transactionmonth_created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="transactionmonth_modified_at", type="datetime", nullable=true)
     */
    private $modifiedAt;

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

    public function getExpected(): ?string
    {
        return $this->expected;
    }

    public function setExpected(?string $expected): self
    {
        $this->expected = $expected;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getCategory(): ?TransactionCategory
    {
        return $this->category;
    }

    public function setCategory(?TransactionCategory $category): self
    {
        $this->category = $category;

        return $this;
    }
}
