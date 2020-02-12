<?php
declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\DeaneryRepositoryInterface;

class DeanerySpy implements DeaneryRepositoryInterface
{
    private $deaneryValue;
    private $getDeaneryByNameCalled = false;
    private $throwException = false;

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
}