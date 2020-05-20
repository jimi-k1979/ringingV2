<?php

declare(strict_types=1);

namespace test\mocks;


use DrlArchive\core\interfaces\repositories\RingerRepositoryInterface;
use traits\CreateMockRingerTrait;

class RingerDummy implements RingerRepositoryInterface
{
    use CreateMockRingerTrait;

    /**
     * @inheritDoc
     */
    public function fuzzySearchRinger(string $searchTerm): array
    {
        return [$this->createMockRinger()];
    }
}