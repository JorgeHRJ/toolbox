<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UrlExtension extends AbstractExtension
{
    private string $domain;

    public function __construct(string $domain)
    {
        $this->domain = $domain;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_absolute_url', [$this, 'getAbsoluteUrl'])
        ];
    }

    public function getAbsoluteUrl(string $path): string
    {
        return sprintf('https://%s%s', $this->domain, $path);
    }
}
