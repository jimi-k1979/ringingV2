<?php
declare(strict_types=1);

namespace core\entities;


use DrlArchive\core\entities\Entity;
use DrlArchive\core\entities\EventEntity;
use DrlArchive\core\entities\TeamEntity;

class ResultEntity extends Entity
{
    /**
     * @var int
     */
    private $position;
    /**
     * @var null|int
     */
    private $pealNumber;
    /**
     * @var float
     */
    private $faults;
    /**
     * @var null|int
     */
    private $points;
    /**
     * @var TeamEntity
     */
    private $team;
    /**
     * @var EventEntity
     */
    private $event;

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return int|null
     */
    public function getPealNumber(): ?int
    {
        return $this->pealNumber;
    }

    /**
     * @param int|null $pealNumber
     */
    public function setPealNumber(?int $pealNumber): void
    {
        $this->pealNumber = $pealNumber;
    }

    /**
     * @return float
     */
    public function getFaults(): float
    {
        return $this->faults;
    }

    /**
     * @param float $faults
     */
    public function setFaults(float $faults): void
    {
        $this->faults = $faults;
    }

    /**
     * @return int|null
     */
    public function getPoints(): ?int
    {
        return $this->points;
    }

    /**
     * @param int|null $points
     */
    public function setPoints(?int $points): void
    {
        $this->points = $points;
    }

    /**
     * @return TeamEntity
     */
    public function getTeam(): TeamEntity
    {
        return $this->team;
    }

    /**
     * @param TeamEntity $team
     */
    public function setTeam(TeamEntity $team): void
    {
        $this->team = $team;
    }

    /**
     * @return EventEntity
     */
    public function getEvent(): EventEntity
    {
        return $this->event;
    }

    /**
     * @param EventEntity $event
     */
    public function setEvent(EventEntity $event): void
    {
        $this->event = $event;
    }

}