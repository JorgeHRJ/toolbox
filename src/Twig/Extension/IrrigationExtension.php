<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IrrigationExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_type_label', [$this, 'getTypeLabel'])
        ];
    }

    public function getTypeLabel(string $type): string
    {
        $labels = [
            'liters_per_week' => 'LITROS POR PLANTA/SEMANAL',
            'liters_per_day' => 'LITROS POR PLANTA/DÍA',
            'pipes_per_week' => 'PIPAS POR CELEMIN/SEMANAL'
        ];

        return $labels[$type] ?? '';
    }
}
