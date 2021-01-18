<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 * @ORM\Table(name="transaction")
 */
class Transaction
{
    use TimestampableTrait;

    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="transaction_id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="El título no puede estar vacío")
     * @Assert\Length(max=128, maxMessage="El título no puede superar {{ limit }} caracteres")
     *
     * @ORM\Column(name="transaction_title", type="string", length=128, nullable=false)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="transaction_amount", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $amount;

    /**
     * @var TransactionMonth|null
     *
     * @ORM\ManyToOne(targetEntity=TransactionMonth::class, inversedBy="transactions")
     * @ORM\JoinColumn(name="transaction_month", referencedColumnName="transactionmonth_id", nullable=false)
     */
    private $month;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="transaction_created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="transaction_modified_at", type="datetime", nullable=true)
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

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getMonth(): ?TransactionMonth
    {
        return $this->month;
    }

    public function setMonth(?TransactionMonth $month): self
    {
        $this->month = $month;

        return $this;
    }
}
