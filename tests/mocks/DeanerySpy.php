<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\DeaneryRepositoryInterface;
use DrlArchive\traits\CreateMockDeaneryTrait;

class DeanerySpy implements DeaneryRepositoryInterface
{

    use CreateMockDeaneryTrait;

    /**
     * @var DeaneryEntity
     */
    private $deaneryValue;
    /**
     * @var bool
     */
    private $getDeaneryByNameCalled = false;
    /**
     * @var bool
     */
    private $selectDeaneryCalled = false;
    /**
     * @var bool
     */
    private $throwException = false;


    public function setRepositoryThrowsException(): void
    {
        $this->throwException = true;
    }

    /**
     * @param string $name
     * @return DeaneryEntity
     * @throws RepositoryNoResults
     */
    public function getDeaneryByName(string $name): DeaneryEntity
    {
        $this->getDeaneryByNameCalled = true;
        if ($this->throwException) {
            throw new RepositoryNoResults();
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
     * @throws RepositoryNoResults
     */
    public function selectDeanery(int $id): DeaneryEntity
    {
        $this->selectDeaneryCalled = true;
        if ($this->throwException) {
            throw new RepositoryNoResults(
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