<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\AbstractCompetitionEntity;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\traits\CreateMockDrlCompetitionTrait;

class CompetitionSpy implements CompetitionRepositoryInterface
{
    use CreateMockDrlCompetitionTrait;

    /**
     * @var bool
     */
    private $throwException = false;
    /**
     * @var DrlCompetitionEntity
     */
    private $insertDrlCompetitionValue;
    /**
     * @var bool
     */
    private $insertDrlCompetitionCalled = false;
    /**
     * @var DrlCompetitionEntity
     */
    private $selectDrlCompetitionValue;
    /**
     * @var bool
     */
    private $selectDrlCompetitionCalled = false;
    /**
     * @var bool
     */
    private $fuzzySearchDrlCompetitionCalled = false;
    /**
     * @var DrlCompetitionEntity[]
     */
    private $fuzzySearchDrlCompetitionValue;
    /**
     * @var bool
     */
    private $fetchDrlCompetitionByLocationCalled = false;
    /**
     * @var bool
     */
    private $fetchDrlCompetitionByLocationCalledThrowsException = false;
    /**
     * @var array
     */
    private $fetchDrlCompetitionByLocationCalledValue = [];
    /**
     * @var bool
     */
    private $fuzzySearchAllCompetitionsCalled = false;
    /**
     * @var bool
     */
    private $fuzzySearchAllCompetitionsThrowsException = false;
    /**
     * @var AbstractCompetitionEntity[]
     */
    private $fuzzySearchAllCompetitionsValue = [];
    /**
     * @var bool
     */
    private $fetchDrlCompetitionByNameCalled = false;
    /**
     * @var bool
     */
    private $fetchDrlCompetitionByNameThrowsException = false;
    /**
     * @var DrlCompetitionEntity
     */
    private $fetchDrlCompetitionByNameValue;


    public function setRepositoryThrowsException(): void
    {
        $this->throwException = true;
    }

    /**
     * @param DrlCompetitionEntity $entity
     * @return DrlCompetitionEntity
     * @throws GeneralRepositoryErrorException
     */
    public function insertDrlCompetition(
        DrlCompetitionEntity $entity
    ): DrlCompetitionEntity {
        $this->insertDrlCompetitionCalled = true;
        if ($this->throwException) {
            throw new GeneralRepositoryErrorException(
                'Unable to add a competition',
                CompetitionRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION
            );
        }
        return $this->insertDrlCompetitionValue ??
            $this->createMockDrlCompetition();
    }

    /**
     * @param DrlCompetitionEntity $entity
     */
    public function setInsertDrlCompetitionValue(
        DrlCompetitionEntity $entity
    ): void {
        $this->insertDrlCompetitionValue = $entity;
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
     * @throws RepositoryNoResults
     */
    public function selectDrlCompetition(int $id): DrlCompetitionEntity
    {
        $this->selectDrlCompetitionCalled = true;
        if ($this->throwException) {
            throw new RepositoryNoResults(
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
     * @throws RepositoryNoResults
     */
    public function fuzzySearchDrlCompetition(string $string): array
    {
        $this->fuzzySearchDrlCompetitionCalled = true;
        if ($this->throwException) {
            throw new RepositoryNoResults(
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
    public function fetchDrlCompetitionByLocationId(int $locationId): array
    {
        $this->fetchDrlCompetitionByLocationCalled = true;
        if ($this->fetchDrlCompetitionByLocationCalledThrowsException) {
            throw new RepositoryNoResults(
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
    public function fuzzySearchAllCompetitions(string $search): array
    {
        $this->fuzzySearchAllCompetitionsCalled = true;
        if ($this->fuzzySearchAllCompetitionsThrowsException) {
            throw new RepositoryNoResults(
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
    public function fetchDrlCompetitionByName(string $competitionName): DrlCompetitionEntity
    {
        $this->fetchDrlCompetitionByNameCalled = true;
        if ($this->fetchDrlCompetitionByNameThrowsException) {
            throw new RepositoryNoResults(
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

    public function setFetchDrlCompetitionByNameValue(array $value): void
    {
        $this->fetchDrlCompetitionByNameValue = $value;
    }

}