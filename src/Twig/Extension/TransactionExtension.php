<?php

namespace App\Twig\Extension;

use App\Entity\TransactionCategory;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TransactionExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('get_peridiocity_text', [$this, 'getPeridiocityText'])
        ];
    }

    public function getPeridiocityText(int $peridiocity): string
    {
        switch ($peridiocity) {
            case TransactionCategory::NO_PERIDIOCITY:
                return 'Sin peridiocidad';
            case TransactionCategory::MONTHLY_PERIDIOCITY:
                return 'Mensual';
            default:
                throw new \Exception(sprintf('Peridiocity %s not handled', $peridiocity));
        }
    }
}
