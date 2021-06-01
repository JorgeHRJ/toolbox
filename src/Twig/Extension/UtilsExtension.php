<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UtilsExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_filter_query', [$this, 'getFilterQuery']),
        ];
    }

    public function getFilterQuery(array $queryParams, array $filters): string
    {
        $allowedParams = array_intersect_key($queryParams, array_flip($filters));

        return sprintf('&%s', http_build_query($allowedParams));
    }
}
