<?php

declare(strict_types=1);

namespace DrlArchive\traits;


trait StripStringTrait
{
    public function stripString(string $stringIn): string
    {
        return preg_replace('/\s+/S', " ", $stringIn);
    }
}