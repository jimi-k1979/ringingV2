<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\JudgeRepositoryInterface;
use DrlArchive\traits\CreateMockDrlEventTrait;
use DrlArchive\traits\CreateMockJudgeTrait;
use DrlArchive\traits\CreateMockRingerTrait;

class JudgeSpy implements JudgeRepositoryInterface
{

    use CreateMockJudgeTrait;
    use CreateMockDrlEventTrait;

    private bool $repositoryThrowsException = false;
    private bool $fetchJudgesByDrlEventCalled = false;
    /**
     * @var JudgeEntity[]|null
     */
    private ?array $fetchJudgesByDrlEventValue = null;
    private bool $fetchJudgeByIdCalled = false;
    private bool $fetchJudgeByIdThrowsException = false;
    private ?JudgeEntity $fetchJudgeByIdValue = null;
    private bool $fetchJudgeDrlEventListCalled = false;
    private bool $fetchJudgeDrlEventListThrowsException = false;
    /**
     * @var DrlEventEntity[]|null
     */
    private array $fetchJudgeDrlEventListValue = [];


    public function setRepositoryThrowsException(): void
    {
        $this->repositoryThrowsException = true;
    }

    /**
     * @param DrlEventEntity $entity
     * @return JudgeEntity[]
     * @throws RepositoryNoResultsException
     */
    public function fetchJudgesByDrlEvent(DrlEventEntity $entity): array
    {
        $this->fetchJudgesByDrlEventCalled = true;
        if ($this->repositoryThrowsException) {
            throw new RepositoryNoResultsException(
                'No judges found',
                JudgeRepositoryInterface::NO_RECORDS_FOUND_EXCEPTION
            );
        }

        return $this->fetchJudgesByDrlEventValue ?? [$this->createMockJudge()];
    }

    public function hasFetchJudgesByDrlEventBeenCalled(): bool
    {
        return $this->fetchJudgesByDrlEventCalled;
    }

    public function setFetchJudgesByDrlEventValue(array $value): void
    {
        $this->fetchJudgesByDrlEventValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchJudgeById(int $id): JudgeEntity
    {
        $this->fetchJudgeByIdCalled = true;
        if ($this->fetchJudgeByIdThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                JudgeRepositoryInterface::NO_RECORDS_FOUND_EXCEPTION,
            );
        }

        return $this->fetchJudgeByIdValue ?? $this->createMockJudge();
    }

    public function hasFetchJudgeByIdBeenCalled(): bool
    {
        return $this->fetchJudgeByIdCalled;
    }

    public function setFetchJudgeByIdThrowsException(): void
    {
        $this->fetchJudgeByIdThrowsException = true;
    }

    public function setFetchJudgeByIdValue(JudgeEntity $value): void
    {
        $this->fetchJudgeByIdValue = $value;
    }


    /**
     * @inheritDoc
     */
    public function fetchJudgeDrlEventList(JudgeEntity $judge): array
    {
        $this->fetchJudgeDrlEventListCalled = true;
        if ($this->fetchJudgeDrlEventListThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                JudgeRepositoryInterface::NO_RECORDS_FOUND_EXCEPTION
            );
        }
        return $this->fetchJudgeDrlEventListValue
            ?? [$this->createMockDrlEvent()];
    }

    public function hasFetchJudgeDrlEventListBeenCalled(): bool
    {
        return $this->fetchJudgeDrlEventListCalled;
    }

    public function setFetchJudgeDrlEventListThrowsException(): void
    {
        $this->fetchJudgeDrlEventListThrowsException = true;
    }

    public function setFetchJudgeDrlEventListValue(array $value): void
    {
        $this->fetchJudgeDrlEventListValue = $value;
    }

}
