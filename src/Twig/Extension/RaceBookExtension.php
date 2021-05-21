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
            new TwigFunction('get_age', [$this, 'getAge'])
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

    public function getAge(\DateTime $date): int
    {
        $today = new \DateTime();
        $diff = $date->diff($today);

        return $diff->y;
    }
}
