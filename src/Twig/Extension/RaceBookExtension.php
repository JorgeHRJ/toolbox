<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RaceBookExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('beautify_win_type', [$this, 'beautifyWinType']),
            new TwigFunction('beautify_grandtour_gc', [$this, 'beautifyGrandTourGc']),
        ];
    }

    public function beautifyWinType(string $type): string
    {
        $labels = [
            'stages' => 'Etapa',
            'gc' => 'Clasificación General',
            'classics' => 'Clásica',
            'itt' => 'Contrarreloj'
        ];

        return $labels[$type] ?? $type;
    }

    public function beautifyGrandTourGc(string $result): string
    {
        $labels = [
            'In progress' => 'En progreso',
        ];

        return $labels[$result] ?? $result;
    }
}
