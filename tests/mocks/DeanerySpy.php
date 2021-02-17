<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\DeaneryRepositoryInterface;
use DrlArchive\traits\CreateMockDeaneryTrait;

class DeanerySpy implements DeaneryRepositoryInterface
{

    use CreateMockDeaneryTrait;

    private DeaneryEntity $deaneryValue;
    private bool $getDeaneryByNameCalled = false;
    private bool $selectDeaneryCalled = false;
    private bool $throwException = false;


    public function setRepositoryThrowsException(): void
    {
        $this->throwException = true;
    }

    /**
     * @param string $name
     * @return DeaneryEntity
     * @throws RepositoryNoResultsException
     */
    public function getDeaneryByName(string $name): DeaneryEntity
    {
        $this->getDeaneryByNameCalled = true;
        if ($this->throwException) {
            throw new RepositoryNoResultsException();
        }

        return $this->deaneryValue ?? new DeaneryEntity();
    }

    /**
     * @param DeaneryEntity $entity
     */
    public function setDeaneryValue(DeaneryEntity $entity): void
    {
        $this->deaneryValue = $entity;
    }

    /**
     * @return bool
     */
    public function hasGetDeaneryByNameBeenCalled(): bool
    {
        return $this->getDeaneryByNameCalled;
    }

    /**
     * @param int $id
     * @return DeaneryEntity
     * @throws RepositoryNoResultsException
     */
    public function selectDeanery(int $id): DeaneryEntity
    {
        $this->selectDeaneryCalled = true;
        if ($this->throwException) {
            throw new RepositoryNoResultsException(
                'No deanery found',
                DeaneryRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->deaneryValue ?? $this->createMockDeanery();
    }

    /**
     * @return bool
     */
    public function hasSelectDeaneryBeenCalled(): bool
    {
        return $this->selectDeaneryCalled;
    }
}