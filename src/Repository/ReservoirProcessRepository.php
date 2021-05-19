<?php

namespace App\Repository;

use App\Entity\ReservoirProcess;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservoirProcessRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservoirProcess::class);
    }

    /**
     * @return string[]|array
     */
    public function getProcessedDates(): array
    {
        $qb = $this->createQueryBuilder('rp');
        $qb
            ->select('rp.date')
            ->where('rp.status = :doneStatus')
            ->setParameter('doneStatus', ReservoirProcess::DONE_STATUS);

        $result = $qb->getQuery()->getResult();

        return array_map(function (array $item) {
            $date = $item['date'];
            return $date->format('d/m/Y');
        }, $result);
    }

    public function updateStatus(array $ids, int $status): void
    {
        $qb = $this->createQueryBuilder('rp');
        $qb
            ->update()
            ->set('rp.status', '?1')
            ->where('rp.id IN (?2)')
            ->setParameter(1, $status)
            ->setParameter(2, $ids);

        $qb->getQuery()->execute();
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
