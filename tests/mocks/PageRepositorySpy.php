<?php

namespace DrlArchive\mocks;

use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interfaces\repositories\PagesRepositoryInterface;

class PageRepositorySpy implements PagesRepositoryInterface
{

    private bool $fetchRecordsPageListCalled = false;
    private array $fetchRecordsPageListValue = [];
    private ?CleanArchitectureException $fetchRecordsPageListException = null;

    public function fetchRecordsPageList(): array
    {
        $this->fetchRecordsPageListCalled = true;
        if ($this->fetchRecordsPageListException) {
            throw $this->fetchRecordsPageListException;
        }
        return $this->fetchRecordsPageListValue;
    }

    public function hasFetchRecordsPageListBeenCalled(): bool
    {
        return $this->fetchRecordsPageListCalled;
    }

    /**
     * @param array $fetchRecordsPageListValue
     */
    public function setFetchRecordsPageListValue(
        array $fetchRecordsPageListValue
    ): void {
        $this->fetchRecordsPageListValue = $fetchRecordsPageListValue;
    }

    public function setFetchRecordsPageListException(
        CleanArchitectureException $exception
    ): void {
        $this->fetchRecordsPageListException = $exception;
    }

}
