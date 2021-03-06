<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\AbstractCompetitionEntity;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\TestConstants;
use DrlArchive\traits\CreateMockDrlCompetitionTrait;

class CompetitionSpy implements CompetitionRepositoryInterface
{
    use CreateMockDrlCompetitionTrait;

    private bool $throwException = false;
    private int $insertDrlCompetitionValue = TestConstants::TEST_DRL_COMPETITION_ID;
    private bool $insertDrlCompetitionCalled = false;
    private DrlCompetitionEntity $selectDrlCompetitionValue;
    private bool $selectDrlCompetitionCalled = false;
    private bool $fuzzySearchDrlCompetitionCalled = false;
    /**
     * @var DrlCompetitionEntity[]
     */
    private array $fuzzySearchDrlCompetitionValue;
    private bool $fetchDrlCompetitionByLocationIdCalled = false;
    private bool $fetchDrlCompetitionByLocationIdCalledThrowsException = false;
    private array $fetchDrlCompetitionByLocationIdCalledValue = [];
    private bool $fuzzySearchAllCompetitionsCalled = false;
    private bool $fuzzySearchAllCompetitionsThrowsException = false;
    /**
     * @var AbstractCompetitionEntity[]
     */
    private array $fuzzySearchAllCompetitionsValue = [];
    private bool $fetchDrlCompetitionByNameCalled = false;
    private bool $fetchDrlCompetitionByNameThrowsException = false;
    private DrlCompetitionEntity $fetchDrlCompetitionByNameValue;
    private bool $fetchDrlCompetitionByLocationCalled = false;
    private bool $fetchDrlCompetitionByLocationCalledThrowsException = false;
    private array $fetchDrlCompetitionByLocationCalledValue = [];
    private bool $fetchDrlCompetitionByUsualLocationCalled = false;
    private bool $fetchDrlCompetitionByUsualLocationThrowsException = false;
    private array $fetchDrlCompetitionByUsualLocationValue = [];
    private bool $fetchDrlCompetitionByVenueCalled = false;
    private bool $fetchDrlCompetitionByVenueThrowsException = false;
    private array $fetchDrlCompetitionByVenueValue = [];


    public function setRepositoryThrowsException(): void
    {
        $this->throwException = true;
    }

    /**
     * @param DrlCompetitionEntity $entity
     * @return void
     * @throws GeneralRepositoryErrorException
     */
    public function insertDrlCompetition(
        DrlCompetitionEntity $entity
    ): void {
        $this->insertDrlCompetitionCalled = true;
        if ($this->throwException) {
            throw new GeneralRepositoryErrorException(
                'Unable to add a competition',
                CompetitionRepositoryInterface::NO_ROWS_CREATED_EXCEPTION
            );
        }
        $entity->setId($this->insertDrlCompetitionValue);
    }

    /**
     * @param int $value
     */
    public function setInsertDrlCompetitionValue(int $value): void
    {
        $this->insertDrlCompetitionValue = $value;
    }

    /**
     * @return bool
     */
    public function hasInsertDrlCompetitionBeenCalled(): bool
    {
        return $this->insertDrlCompetitionCalled;
    }

    /**
     * @param int $id
     * @return DrlCompetitionEntity
     * @throws RepositoryNoResultsException
     */
    public function selectDrlCompetition(int $id): DrlCompetitionEntity
    {
        $this->selectDrlCompetitionCalled = true;
        if ($this->throwException) {
            throw new RepositoryNoResultsException(
                'No competition found',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }
        return $this->selectDrlCompetitionValue ??
            $this->createMockDrlCompetition();
    }

    /**
     * @param DrlCompetitionEntity $entity
     */
    public function setSelectDrlCompetitionValue(
        DrlCompetitionEntity $entity
    ): void {
        $this->selectDrlCompetitionValue = $entity;
    }

    /**
     * @return bool
     */
    public function hasSelectCompetitionBeenCalled(): bool
    {
        return $this->selectDrlCompetitionCalled;
    }

    /**
     * @inheritDoc
     * @throws RepositoryNoResultsException
     */
    public function fuzzySearchDrlCompetitions(string $string): array
    {
        $this->fuzzySearchDrlCompetitionCalled = true;
        if ($this->throwException) {
            throw new RepositoryNoResultsException(
                'No competitions found',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fuzzySearchDrlCompetitionValue ?? [
                $this->createMockDrlCompetition()
            ];
    }

    /**
     * @return bool
     */
    public function hasFuzzySearchDrlCompetitionBeenCalled(): bool
    {
        return $this->fuzzySearchDrlCompetitionCalled;
    }

    /**
     * @param DrlCompetitionEntity[] $drlCompetitionEntities
     */
    public function setFuzzySearchDrlCompetitionValue(
        array $drlCompetitionEntities
    ): void {
        $this->fuzzySearchDrlCompetitionValue = $drlCompetitionEntities;
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlCompetitionByUsualLocationId(int $locationId): array
    {
        $this->fetchDrlCompetitionByLocationIdCalled = true;
        if ($this->fetchDrlCompetitionByLocationIdCalledThrowsException) {
            throw new RepositoryNoResultsException(
                'No rows found',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fetchDrlCompetitionByLocationIdCalledValue;
    }

    public function hasFetchDrlCompetitionByLocationIdBeenCalled(): bool
    {
        return $this->fetchDrlCompetitionByLocationIdCalled;
    }

    public function setFetchDrlCompetitionByLocationIdThrowsException(): void
    {
        $this->fetchDrlCompetitionByLocationIdCalledThrowsException = true;
    }

    public function setFetchDrlCompetitionByLocationIdValue(array $value): void
    {
        $this->fetchDrlCompetitionByLocationIdCalledValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fuzzySearchAllCompetitions(string $search): array
    {
        $this->fuzzySearchAllCompetitionsCalled = true;
        if ($this->fuzzySearchAllCompetitionsThrowsException) {
            throw new RepositoryNoResultsException(
                'No competitions found',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }
        return $this->fuzzySearchAllCompetitionsValue;
    }

    public function hasFuzzySearchAllCompetitionsBeenCalled(): bool
    {
        return $this->fuzzySearchAllCompetitionsCalled;
    }

    public function setFuzzySearchAllCompetitionsThrowsException(): void
    {
        $this->fuzzySearchAllCompetitionsThrowsException = true;
    }

    public function setFuzzySearchAllCompetitionsValue(array $value): void
    {
        $this->fuzzySearchAllCompetitionsValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlCompetitionByName(
        string $competitionName
    ): DrlCompetitionEntity {
        $this->fetchDrlCompetitionByNameCalled = true;
        if ($this->fetchDrlCompetitionByNameThrowsException) {
            throw new RepositoryNoResultsException(
                'No competition found',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }
        return $this->fetchDrlCompetitionByNameValue ??
            $this->createMockDrlCompetition();
    }

    public function hasFetchDrlCompetitionByNameBeenCalled(): bool
    {
        return $this->fetchDrlCompetitionByNameCalled;
    }

    public function setFetchDrlCompetitionByNameThrowsException(): void
    {
        $this->fetchDrlCompetitionByNameThrowsException = true;
    }

    public function setFetchDrlCompetitionByNameValue(
        DrlCompetitionEntity $value
    ): void {
        $this->fetchDrlCompetitionByNameValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fuzzySearchOtherCompetitions(string $search): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlCompetitionByLocation(LocationEntity $location): array
    {
        $this->fetchDrlCompetitionByLocationCalled = true;
        if ($this->fetchDrlCompetitionByLocationCalledThrowsException) {
            throw new RepositoryNoResultsException(
                'No rows found',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fetchDrlCompetitionByLocationCalledValue;
    }

    public function hasFetchDrlCompetitionByLocationBeenCalled(): bool
    {
        return $this->fetchDrlCompetitionByLocationCalled;
    }

    public function setFetchDrlCompetitionByLocationThrowsException(): void
    {
        $this->fetchDrlCompetitionByLocationCalledThrowsException = true;
    }

    public function setFetchDrlCompetitionByLocationValue(array $value): void
    {
        $this->fetchDrlCompetitionByLocationCalledValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlCompetitionByUsualLocation(LocationEntity $location): array
    {
        $this->fetchDrlCompetitionByUsualLocationCalled = true;
        if ($this->fetchDrlCompetitionByUsualLocationThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fetchDrlCompetitionByUsualLocationValue;
    }

    public function hasFetchDrlCompetitionByUsualLocationBeenCalled(): bool
    {
        return $this->fetchDrlCompetitionByUsualLocationCalled;
    }

    public function setFetchDrlCompetitionByUsualLocationThrowsException(): void
    {
        $this->fetchDrlCompetitionByUsualLocationThrowsException = true;
    }

    public function setFetchDrlCompetitionByUsualLocationValue(array $value): void
    {
        $this->fetchDrlCompetitionByUsualLocationValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlCompetitionByVenue(LocationEntity $location): array
    {
        $this->fetchDrlCompetitionByVenueCalled = true;
        if ($this->fetchDrlCompetitionByVenueThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fetchDrlCompetitionByVenueValue;
    }

    public function hasFetchDDrlCompetitionByVenueBeenCalled(): bool
    {
        return $this->fetchDrlCompetitionByVenueCalled;
    }

    public function setFetchDDrlCompetitionByVenueThrowsException(): void
    {
        $this->fetchDrlCompetitionByVenueThrowsException = true;
    }

    public function setFetchDDrlCompetitionByVenueValue(array $value): void
    {
        $this->fetchDrlCompetitionByVenueValue = $value;
    }

}
