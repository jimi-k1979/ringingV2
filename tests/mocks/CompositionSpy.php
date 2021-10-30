<?php

declare(strict_types=1);

namespace DrlArchive\mocks;

use DrlArchive\core\entities\CompositionEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\CompositionRepositoryInterface;
use DrlArchive\traits\CreateMockCompositionTrait;

class CompositionSpy implements CompositionRepositoryInterface
{
    use CreateMockCompositionTrait;

    private bool $fetchAllCompositionsCalled = false;
    private bool $fetchAllCompositionsThrowsException = false;
    private ?array $fetchAllCompositionsValue = null;
    private bool $fetchCompositionByIdCalled = false;
    private bool $fetchCompositionByIdThrowsException = false;
    private ?CompositionEntity $fetchCompositionByIdValue = null;
    private bool $fetchChangesByCompositionCalled = false;
    private bool $fetchChangesByCompositionThrowsException = false;
    private ?array $fetchChangesByCompositionValue = null;

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

    /**
     * @inheritDoc
     */
    public function fetchCompositionById(int $id): CompositionEntity
    {
        $this->fetchCompositionByIdCalled = true;
        if ($this->fetchCompositionByIdThrowsException) {
            throw new RepositoryNoResultsException(
                'Something went wrong',
                CompositionRepositoryInterface::NO_RESULTS_FOUND_EXCEPTION_CODE
            );
        }
        return $this->fetchCompositionByIdValue
            ?? $this->createMockComposition();
    }

    public function hasFetchCompositionByIdBeenCalled(): bool
    {
        return $this->fetchCompositionByIdCalled;
    }

    public function setFetchCompositionByIdThrowsException(): void
    {
        $this->fetchCompositionByIdThrowsException = true;
    }

    public function setFetchCompositionByIdValue(
        CompositionEntity $value
    ): void {
        $this->fetchCompositionByIdValue = $value;
    }

    /**
     * @inheritDoc
     */
    public function fetchChangesByComposition(CompositionEntity $composition): void
    {
        $this->fetchChangesByCompositionCalled = true;
        if ($this->fetchChangesByCompositionThrowsException) {
            throw new CleanArchitectureException(
                'something went wrong'
            );
        }

        $composition->setChanges(
            $this->fetchChangesByCompositionValue ??
            [$this->createMockChange()]
        );
    }

    public function hasFetchChangesByCompositionBeenCalled(): bool
    {
        return $this->fetchChangesByCompositionCalled;
    }

    public function setFetchChangesByCompositionThrowsException(): void
    {
        $this->fetchChangesByCompositionThrowsException = true;
    }

    public function setFetchChangesByCompositionValue(array $value): void
    {
        $this->fetchChangesByCompositionValue = $value;
    }

}
