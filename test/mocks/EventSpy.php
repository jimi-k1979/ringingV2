<?php

declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use traits\CreateMockDrlEventTrait;

class EventSpy implements EventRepositoryInterface
{
    use CreateMockDrlEventTrait;

    /**
     * @var bool
     */
    private $insertEventCalled = false;
    /**
     * @var bool
     */
    private $throwException = false;
    /**
     * @var DrlEventEntity|null
     */
    private $drlEventValue;

    public function setThrowException(): void
    {
        $this->throwException = true;
    }

    /**
     * @param DrlEventEntity $entity
     * @return DrlEventEntity
     * @throws GeneralRepositoryErrorException
     */
    public function insertEvent(DrlEventEntity $entity): DrlEventEntity
    {
        $this->insertEventCalled = true;
        if ($this->throwException) {
            throw new GeneralRepositoryErrorException(
                "Can't insert event",
                EventRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION
            );
        }

        return $this->drlEventValue ?? $this->createMockDrlEvent();
    }

    /**
     * @return bool
     */
    public function hasInsertEventBeenCalled(): bool
    {
        return $this->insertEventCalled;
    }

    /**
     * @param DrlEventEntity|null $drlEventValue
     */
    public function setDrlEventValue(?DrlEventEntity $drlEventValue): void
    {
        $this->drlEventValue = $drlEventValue;
    }

    public function selectCompetition(int $id): DrlEventEntity
    {
    }
}