<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\JudgeRepositoryInterface;
use DrlArchive\traits\CreateMockJudgeTrait;

class JudgeSpy implements JudgeRepositoryInterface
{

    use CreateMockJudgeTrait;

    private bool $repositoryThrowsException = false;
    private bool $fetchJudgesByDrlEventCalled = false;
    /**
     * @var JudgeEntity[]
     */
    private array $fetchJudgesByDrlEventValue;

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
}