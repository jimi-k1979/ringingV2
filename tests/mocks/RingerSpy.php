<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\RingerEntity;
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
    private ?array $fuzzySearchValue;

    private bool $fetchWinningTeamByEventCalled = false;
    private bool $fetchWinningTeamByEventThrowsException = false;
    private array $fetchWinningTeamByEventValue = [];

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

}
