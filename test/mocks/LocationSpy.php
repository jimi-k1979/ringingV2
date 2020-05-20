<?php
declare(strict_types=1);

namespace test\mocks;


use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;
use traits\CreateMockLocationTrait;

class LocationSpy implements LocationRepositoryInterface
{

    use CreateMockLocationTrait;

    /**
     * @var bool
     */
    private $repositoryThrowsException = false;
    /**
     * @var bool
     */
    private $insertLocationCalled = false;
    /**
     * @var LocationEntity
     */
    private $insertLocationValue;
    /**
     * @var bool
     */
    private $selectLocationCalled = false;
    /**
     * @var LocationEntity
     */
    private $selectLocationValue;
    /**
     * @var bool
     */
    private $fuzzySearchLocationCalled;
    private $fuzzySearchValue = [];

    public function setRepositoryThrowsException(): void
    {
        $this->repositoryThrowsException = true;
    }

    /**
     * @param LocationEntity $locationEntity
     * @return LocationEntity
     * @throws GeneralRepositoryErrorException
     */
    public function insertLocation(
        LocationEntity $locationEntity
    ): LocationEntity {
        $this->insertLocationCalled = true;
        if ($this->repositoryThrowsException) {
            throw new GeneralRepositoryErrorException(
                'Unable to write new location',
                LocationRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION
            );
        }

        return $this->insertLocationValue ?? $this->createMockLocation();
    }

    /**
     * @return bool
     */
    public function hasInsertLocationBeenCalled(): bool
    {
        return $this->insertLocationCalled;
    }

    /**
     * @param LocationEntity $entity
     */
    public function setInsertLocationValue(LocationEntity $entity): void
    {
        $this->insertLocationValue = $entity;
    }

    /**
     * @param int $locationId
     * @return LocationEntity
     * @throws RepositoryNoResults
     */
    public function selectLocation(int $locationId): LocationEntity
    {
        $this->selectLocationCalled = true;
        if ($this->repositoryThrowsException) {
            throw new RepositoryNoResults(
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


}