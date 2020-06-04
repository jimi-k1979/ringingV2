<?php

declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\DrlResultEntity;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\ResultRepositoryInterface;
use traits\CreateMockDrlResultTrait;

class ResultSpy implements ResultRepositoryInterface
{
    use CreateMockDrlResultTrait;

    /**
     * @var bool
     */
    private $insertDrlResultCalled = false;
    /**
     * @var bool
     */
    private $createThrowsException = false;
    /**
     * @var DrlResultEntity
     */
    private $insertDrlResultValue;
    /**
     * @var bool
     */
    private $fetchDrlEventResultsCalled = false;
    /**
     * @var bool
     */
    private $fetchDrlEventResultsThrowsException = false;
    /**
     * @var DrlResultEntity[]
     */
    private $fetchDrlEventResultValue;


    public function insertDrlResult(
        DrlResultEntity $resultEntity
    ): DrlResultEntity {
        $this->insertDrlResultCalled = true;
        if ($this->createThrowsException) {
            throw new GeneralRepositoryErrorException(
                'Unable to create result',
                ResultRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION
            );
        }
        return $this->insertDrlResultValue ?? $this->createMockDrlResult();
    }

    /**
     */
    public function setCreateThrowsException(): void
    {
        $this->createThrowsException = true;
    }

    /**
     * @param DrlResultEntity $insertDrlResultValue
     */
    public function setInsertDrlResultValue(
        DrlResultEntity $insertDrlResultValue
    ): void {
        $this->insertDrlResultValue = $insertDrlResultValue;
    }

    /**
     * @return bool
     */
    public function hasInsertDrlResultBeenCalled(): bool
    {
        return $this->insertDrlResultCalled;
    }


    /**
     * @inheritDoc
     * @throws RepositoryNoResults
     */
    public function fetchDrlEventResults(DrlEventEntity $eventEntity): array
    {
        $this->fetchDrlEventResultsCalled = true;
        if ($this->fetchDrlEventResultsThrowsException) {
            throw new RepositoryNoResults(
                'Unable to create result',
                ResultRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }
        return $this->fetchDrlEventResultValue ??
            $this->createMockEventResults();
    }

    /**
     */
    public function setFetchDrlEventResultsThrowsException(): void
    {
        $this->fetchDrlEventResultsThrowsException = true;
    }

    /**
     * @param DrlResultEntity[] $fetchDrlEventResultValue
     */
    public function setFetchDrlEventResultValue(
        array $fetchDrlEventResultValue
    ): void {
        $this->fetchDrlEventResultValue = $fetchDrlEventResultValue;
    }

    /**
     * @return bool
     */
    public function hasFetchDrlEventResultsBeenCalled(): bool
    {
        return $this->fetchDrlEventResultsCalled;
    }


}