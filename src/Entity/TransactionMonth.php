<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\TransactionMonthRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\ManyToOne(targetEntity=TransactionCategory::class, inversedBy="months")
     * @ORM\JoinColumn(name="transactionmonth_category", referencedColumnName="transactioncategory_id", nullable=false)
     */
    private $category;

    /**
     * @var Collection|null
     *
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="month", cascade={"persist", "remove"})
     */
    private $transactions;

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

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

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

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    /**
     * @param Transaction $transaction
     * @return $this
     */
    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setMonth($this);
        }

        return $this;
    }

    /**
     * @param Transaction $transaction
     * @return $this
     */
    public function removeMonth(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getMonth() === $this) {
                $transaction->setMonth(null);
            }
        }

        return $this;
    }
}
