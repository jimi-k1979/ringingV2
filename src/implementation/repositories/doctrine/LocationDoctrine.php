<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;


use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;

class LocationDoctrine extends DoctrineRepository implements
    LocationRepositoryInterface
{

    public function insertLocation(LocationEntity $locationEntity): void
    {
        // TODO: Implement insertLocation() method.
    }

    public function selectLocation(int $locationId): LocationEntity
    {
        // TODO: Implement selectLocation() method.
    }

    /**
     * @inheritDoc
     */
    public function fuzzySearchLocation(string $search): array
    {
        // TODO: Implement fuzzySearchLocation() method.
    }

    /**
     * @inheritDoc
     */
    public function fetchLocationByName(string $name): LocationEntity
    {
        // TODO: Implement fetchLocationByName() method.
        
    }
}
