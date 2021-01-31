<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;

interface LocationRepositoryInterface
{
    public const UNABLE_TO_INSERT_EXCEPTION = 2101;
    public const NO_ROWS_FOUND_EXCEPTION = 2102;

    public function insertLocation(
        LocationEntity $locationEntity
    ): void;

    public function selectLocation(int $locationId): LocationEntity;

    /**
     * @param string $search
     * @return LocationEntity[]
     * @throws CleanArchitectureException
     */
    public function fuzzySearchLocation(string $search): array;

    /**
     * @param string $name
     * @return LocationEntity
     * @throws CleanArchitectureException
     */
    public function fetchLocationByName(string $name): LocationEntity;

}