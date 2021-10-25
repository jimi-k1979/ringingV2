<?php

declare(strict_types=1);

namespace DrlArchive\mocks;

use DrlArchive\core\entities\CompositionEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interfaces\repositories\CompositionRepositoryInterface;
use DrlArchive\traits\CreateMockCompositionTrait;

class CompositionSpy implements CompositionRepositoryInterface
{
    use CreateMockCompositionTrait;

    private bool $fetchAllCompositionsCalled = false;
    private bool $fetchAllCompositionsThrowsException = false;
    private ?array $fetchAllCompositionsValue = null;

    /**
     * @inheritDoc
     */
    public function fetchAllCompositions(): array
    {
        $this->fetchAllCompositionsCalled = true;
        if ($this->fetchAllCompositionsThrowsException) {
            throw new CleanArchitectureException(
                'something went wrong'
            );
        }

        return $this->fetchAllCompositionsValue
            ?? [$this->createMockComposition()];
    }

    public function hasFetchAllCompositionsBeenCalled(): bool
    {
        return $this->fetchAllCompositionsCalled;
    }

    public function setFetchAllCompositionsThrowsException(): void
    {
        $this->fetchAllCompositionsThrowsException = true;
    }

    public function setFetchAllCompositionsValue(array $value): void
    {
        $this->fetchAllCompositionsValue = $value;
    }

}
