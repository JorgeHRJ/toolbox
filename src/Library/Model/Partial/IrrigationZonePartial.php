<?php

namespace App\Library\Model\Partial;

class IrrigationZonePartial
{
    private int $id;
    private string $name;
    private \DateTime $recentStartDate;
    private \DateTime $recentEndDate;

    public function __construct(int $id, string $name, \DateTime $recentStartDate, \DateTime $recentEndDate)
    {
        $this->id = $id;
        $this->name = $name;
        $this->recentStartDate = $recentStartDate;
        $this->recentEndDate = $recentEndDate;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \DateTime
     */
    public function getRecentStartDate(): \DateTime
    {
        return $this->recentStartDate;
    }

    /**
     * @param \DateTime $recentStartDate
     */
    public function setRecentStartDate(\DateTime $recentStartDate): void
    {
        $this->recentStartDate = $recentStartDate;
    }

    /**
     * @return \DateTime
     */
    public function getRecentEndDate(): \DateTime
    {
        return $this->recentEndDate;
    }

    /**
     * @param \DateTime $recentEndDate
     */
    public function setRecentEndDate(\DateTime $recentEndDate): void
    {
        $this->recentEndDate = $recentEndDate;
    }
}
