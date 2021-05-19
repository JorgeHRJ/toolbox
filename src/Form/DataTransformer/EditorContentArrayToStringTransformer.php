<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class EditorContentArrayToStringTransformer implements DataTransformerInterface
{
    /**
     * @param array $value
     * @return string
     */
    public function transform($value): string
    {
        return json_encode($value);
    }

    /**
     * @param string $value
     * @return array
     */
    public function reverseTransform($value): array
    {
        return json_decode($value, true);
    }
}
