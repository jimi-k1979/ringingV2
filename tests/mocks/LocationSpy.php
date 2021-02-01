<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;
use DrlArchive\TestConstants;
use DrlArchive\traits\CreateMockLocationTrait;

class LocationSpy implements LocationRepositoryInterface
{
    use CreateMockLocationTrait;

    private bool $insertThrowsException = false;
    private bool $insertLocationCalled = false;
    private int $insertLocationIdValue = TestConstants::TEST_LOCATION_ID;
    private bool $selectLocationCalled = false;
    private LocationEntity $selectLocationValue;
    private bool $fuzzySearchLocationCalled = false;
    /**
     * @var LocationEntity[]
     */
    private array $fuzzySearchValue = [];
    private bool $selectLocationThrowsException = false;

    /**
     * @param LocationEntity $location
     * @return void
     * @throws GeneralRepositoryErrorException
     */
    public function insertLocation(
        LocationEntity $location
    ): void {
        $this->insertLocationCalled = true;
        if ($this->insertThrowsException) {
            throw new GeneralRepositoryErrorException(
                'Unable to write new location',
                LocationRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION
            );
        }

        $location->setId($this->insertLocationIdValue);
    }

    /**
     * @return bool
     */
    public function hasInsertLocationBeenCalled(): bool
    {
        return $this->insertLocationCalled;
    }

    public function setInsertThrowsException(): void
    {
        $this->insertThrowsException = true;
    }

    /**
     * @param int $entity
     */
    public function setInsertLocationIdValue(int $entity): void
    {
        $this->insertLocationIdValue = $entity;
    }

    /**
     * @param int $locationId
     * @return LocationEntity
     * @throws RepositoryNoResultsException
     */
    public function fetchLocationById(int $locationId): LocationEntity
    {
        $this->selectLocationCalled = true;
        if ($this->selectLocationThrowsException) {
            throw new RepositoryNoResultsException(
                'No location found',
                LocationRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->selectLocationValue ?? $this->createMockLocation();
    }

    /**
     * @return bool
     */
    public function hasSelectLocationBeenCalled(): bool
    {
        return $this->selectLocationCalled;
    }

    /**
     * @param LocationEntity $entity
     */
    public function setSelectLocationValue(LocationEntity $entity): void
    {
        $this->selectLocationValue = $entity;
    }

    public function setSelectLocationThrowsException(): void
    {
        $this->selectLocationThrowsException = true;
    }

    /**
     * @inheritDoc
     */
    public function fuzzySearchLocation(string $search): array
    {
        $this->fuzzySearchLocationCalled = true;

        return $this->fuzzySearchValue ?? [$this->createMockLocation()];
    }

    /**
     * @return bool
     */
    public function hasFuzzySearchLocationBeenCalled(): bool
    {
        return $this->fuzzySearchLocationCalled;
    }

    /**
     * @param array $fuzzySearchValue
     */
    public function setFuzzySearchValue(array $fuzzySearchValue): void
    {
        $this->fuzzySearchValue = $fuzzySearchValue;
    }


    /**
     * @inheritDoc
     */
    public function fetchLocationByName(string $name): LocationEntity
    {
        $this->fetchLocationByNameCalled = true;
        if ($this->fetchLocationByNameThrowsException) {
            throw new CleanArchitectureException(
                'No location with that name',
                LocationRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fetchLocationByNameValue ?? $this->createMockLocation();
    }

    public function hasFetchLocationByNameBeenCalled(): bool
    {
        return $this->fetchLocationByNameCalled;
    }

    public function setFetchLocationByNameThrowsException(): void
    {
        $this->fetchLocationByNameThrowsException = true;
    }

    public function setFetchLocationByNameValue(LocationEntity $value): void
    {
        $this->fetchLocationByNameValue = $value;
    }

    private bool $fetchLocationByNameCalled = false;
    private bool $fetchLocationByNameThrowsException = false;
    private LocationEntity $fetchLocationByNameValue;
}
