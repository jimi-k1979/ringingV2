<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\RingerEntity;
use DrlArchive\core\entities\WinningRingerEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\RingerRepositoryInterface;
use DrlArchive\traits\CreateMockRingerTrait;

class RingerSpy implements RingerRepositoryInterface
{
    use CreateMockRingerTrait;

    private bool $fuzzySearchRingerCalled = false;
    private bool $fuzzySearchThrowsException = false;
    /**
     * @var null|RingerEntity[]
     */
    private ?array $fuzzySearchValue = null;
    private bool $fetchWinningTeamByEventCalled = false;
    private bool $fetchWinningTeamByEventThrowsException = false;
    /**
     * @var WinningRingerEntity[]|null
     */
    private ?array $fetchWinningTeamByEventValue = null;
    private bool $fetchRingerByIdCalled = false;
    private bool $fetchRingerByIdThrowsException = false;
    private ?RingerEntity $fetchRingerByIdValue = null;
    private bool $fetchRingerEventListCalled = false;
    private bool $fetchRingerEventListThrowsException = false;
    /**
     * @var WinningRingerEntity[]|null
     */
    private ?array $fetchRingerEventListValue = null;

    /**
     * @inheritDoc
     * @throws RepositoryNoResultsException
     */
    public function fuzzySearchRinger(string $searchTerm): array
    {
        $this->fuzzySearchRingerCalled = true;

        if ($this->fuzzySearchThrowsException) {
            throw new RepositoryNoResultsException(
                'No ringers found',
                RingerRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fuzzySearchValue ?? [$this->createMockRinger()];
    }

    /**
     * @param RingerEntity[]|null $fuzzySearchValue
     */
    public function setFuzzySearchValue(?array $fuzzySearchValue): void
    {
        $this->fuzzySearchValue = $fuzzySearchValue;
    }

    public function setFuzzySearchThrowsException(): void
    {
        $this->fuzzySearchThrowsException = true;
    }

    /**
     * @return bool
     */
    public function hasFuzzySearchRingerBeenCalled(): bool
    {
        return $this->fuzzySearchRingerCalled;
    }

    /**
     * @inheritDoc
     */
    public function fetchWinningTeamByEvent(DrlEventEntity $event): array
    {
        $this->fetchWinningTeamByEventCalled = true;
        if ($this->fetchWinningTeamByEventThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong'
            );
        }

        return $this->fetchWinningTeamByEventValue;
    }

    public function hasFetchWinningTeamByEventBeenCalled(): bool
    {
        return $this->fetchWinningTeamByEventCalled;
    }

    public function setFetchWinningTeamByEventThrowsException(): void
    {
        $this->fetchWinningTeamByEventThrowsException = true;
    }

    public function setFetchWinningTeamByEventValue(array $value): void
    {
        $this->fetchWinningTeamByEventValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchRingerById(int $ringerId): RingerEntity
    {
        $this->fetchRingerByIdCalled = true;
        if ($this->fetchRingerByIdThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                RingerRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fetchRingerByIdValue ?? $this->createMockRinger();
    }

    public function hasFetchRingerByIdBeenCalled(): bool
    {
        return $this->fetchRingerByIdCalled;
    }

    public function setFetchRingerByIdThrowsException(): void
    {
        $this->fetchRingerByIdThrowsException = true;
    }

    public function setFetchRingerByIdValue(RingerEntity $value): void
    {
        $this->fetchRingerByIdValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchWinningRingerDetailsByRinger(RingerEntity $ringer): array
    {
        $this->fetchRingerEventListCalled = true;
        if ($this->fetchRingerEventListThrowsException) {
            throw new CleanArchitectureException(
                'Something went wrong',
                RingerRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fetchRingerEventListValue
            ?? [$this->createMockWinningRinger()];
    }

    public function hasFetchRingerEventListBeenCalled(): bool
    {
        return $this->fetchRingerEventListCalled;
    }

    public function setFetchRingerEventListThrowsException(): void
    {
        $this->fetchRingerEventListThrowsException = true;
    }

    public function setFetchRingerEventListValue(array $value): void
    {
        $this->fetchRingerEventListValue = $value;
    }

}
