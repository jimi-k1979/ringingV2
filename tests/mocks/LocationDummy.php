<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;
use traits\CreateMockLocationTrait;

class LocationDummy implements LocationRepositoryInterface
{
    use CreateMockLocationTrait;

    public function insertLocation(LocationEntity $locationEntity): LocationEntity
    {
        return $this->createMockLocation();
    }

    public function selectLocation(int $locationId): LocationEntity
    {
        return $this->createMockLocation();
    }

    /**
     * @inheritDoc
     */
    public function fuzzySearchLocation(string $search): array
    {
        return [$this->createMockLocation()];
    }
}