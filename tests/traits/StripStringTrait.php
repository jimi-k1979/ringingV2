<?php

declare(strict_types=1);

namespace traits;


trait StripStringTrait
{
    public function stripString(string $stringIn): string
    {
        return preg_replace('/\s+/S', " ", $stringIn);
    }
}