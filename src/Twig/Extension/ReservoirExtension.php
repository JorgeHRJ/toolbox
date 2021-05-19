<?php

namespace App\Twig\Extension;

use App\Entity\ReservoirData;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ReservoirExtension extends AbstractExtension
{
    const RESERVOIR_DATA_CAPACITY_CACHE_KEY = 'reservoir_data_%d_capacity';

    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_reservoir_data_capacity', [$this, 'getReservoirDataCapacity'])
        ];
    }

    public function getReservoirDataCapacity(ReservoirData $reservoirData): int
    {
        $key = sprintf(self::RESERVOIR_DATA_CAPACITY_CACHE_KEY, $reservoirData->getId());
        return $this->cache->get($key, function (ItemInterface $item) use ($reservoirData) {
            $item->expiresAfter(604800); // 1 week

            $value = round($reservoirData->getReservoir()->getCapacity() * ($reservoirData->getFillness() / 100));
            $item->set($value);

            return $value;
        });
    }
}
