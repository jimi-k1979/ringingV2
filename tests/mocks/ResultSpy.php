<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\DrlResultEntity;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\ResultRepositoryInterface;
use DrlArchive\traits\CreateMockDrlResultTrait;

class ResultSpy implements ResultRepositoryInterface
{
    use CreateMockDrlResultTrait;

    private bool $insertDrlResultCalled = false;
    private bool $createThrowsException = false;
    private int $insertDrlResultCallCount = 0;
    private int $insertDrlResultIdValue = 0;
    private bool $fetchDrlEventResultsCalled = false;
    private bool $fetchDrlEventResultsThrowsException = false;
    /**
     * @var DrlResultEntity[]
     */
    private array $fetchDrlEventResultValue;


    public function insertDrlResult(
        DrlResultEntity $resultEntity
    ): void {
        $this->insertDrlResultCalled = true;
        $this->insertDrlResultCallCount++;

        if ($this->createThrowsException) {
            throw new GeneralRepositoryErrorException(
                'Unable to create result',
                ResultRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION
            );
        }

        $resultEntity->setId($this->insertDrlResultIdValue);
    }

    /**
     */
    public function setCreateThrowsException(): void
    {
        $this->createThrowsException = true;
    }

    /**
     * @param DrlResultEntity $insertDrlResultIdValue
     */
    public function setInsertDrlResultIdValue(
        int $insertDrlResultIdValue
    ): void {
        $this->insertDrlResultIdValue = $insertDrlResultIdValue;
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
