<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\RingerEntity;
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

}