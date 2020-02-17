<?php
declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\DeaneryRepositoryInterface;
use traits\CreateMockDeaneryTrait;

class DeanerySpy implements DeaneryRepositoryInterface
{

    use CreateMockDeaneryTrait;

    private $deaneryValue;
    private $getDeaneryByNameCalled = false;
    private $throwException = false;
    /**
     * @var bool
     */
    private $selectDeaneryCalled = false;

    public function getDeaneryByName(string $name): DeaneryEntity
    {
        $this->getDeaneryByNameCalled = true;
        if ($this->throwException) {
            throw new RepositoryNoResults();
        }

        return $this->deaneryValue ?? new DeaneryEntity();
    }

    public function setDeaneryValue(DeaneryEntity $entity): void
    {
        $this->deaneryValue = $entity;
    }

    public function hasGetDeaneryByNameBeenCalled(): bool
    {
        return $this->getDeaneryByNameCalled;
    }

    public function getDeaneryByNameThrowsException(): void
    {
        $this->throwException = true;
    }

    public function selectDeanery(int $deaneryId): DeaneryEntity
    {
        $this->selectDeaneryCalled = true;

        return $this->deaneryValue ?? $this->createMockDeanery();
    }
}