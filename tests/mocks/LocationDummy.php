<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;
use DrlArchive\traits\CreateMockLocationTrait;

class LocationDummy implements LocationRepositoryInterface
{
    use CreateMockLocationTrait;

    public function insertLocation(LocationEntity $locationEntity): void
    {
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

    /**
     * @inheritDoc
     */
    public function fetchLocationByName(string $name): LocationEntity
    {
        return $this->createMockLocation();
    }
}
