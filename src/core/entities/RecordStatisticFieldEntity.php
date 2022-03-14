<?php

namespace DrlArchive\core\entities;

class RecordStatisticFieldEntity
{
    private ?TeamEntity $team = null;
    private ?AbstractEventEntity $event = null;
    private ?AbstractResultEntity $result = null;
    private float $recordValue;
    private array $associatedValues = [];

    /**
     * @return TeamEntity|null
     */
    public function getTeam(): ?TeamEntity
    {
        return $this->team;
    }

    /**
     * @param TeamEntity|null $team
     */
    public function setTeam(?TeamEntity $team): void
    {
        $this->team = $team;
    }

    /**
     * @return AbstractEventEntity|null
     */
    public function getEvent(): ?AbstractEventEntity
    {
        return $this->event;
    }

    /**
     * @param AbstractEventEntity|null $event
     */
    public function setEvent(?AbstractEventEntity $event): void
    {
        $this->event = $event;
    }

    /**
     * @return AbstractResultEntity|null
     */
    public function getResult(): ?AbstractResultEntity
    {
        return $this->result;
    }

    /**
     * @param AbstractResultEntity|null $result
     */
    public function setResult(?AbstractResultEntity $result): void
    {
        $this->result = $result;
    }

    /**
     * @return float
     */
    public function getRecordValue(): float
    {
        return $this->recordValue;
    }

    /**
     * @param float $recordValue
     */
    public function setRecordValue(float $recordValue): void
    {
        $this->recordValue = $recordValue;
    }

    /**
     * @return array
     */
    public function getAssociatedValues(): array
    {
        return $this->associatedValues;
    }

    /**
     * @param array $associatedValues
     */
    public function setAssociatedValues(array $associatedValues): void
    {
        $this->associatedValues = $associatedValues;
    }
    
}
