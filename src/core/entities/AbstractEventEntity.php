<?php
declare(strict_types=1);

namespace DrlArchive\core\entities;


abstract class AbstractEventEntity extends Entity
{
    public const EVENT_TYPE_OTHER = 0;
    public const EVENT_TYPE_DRL = 1;
    public const EVENT_TYPE_LADDER = 2;

    private string $year;
    private AbstractCompetitionEntity $competition;
    private LocationEntity $location;
    /**
     * @var null|JudgeEntity[]
     */
    private ?array $judges;
    private bool $unusualTower = false;

    /**
     * @return string
     */
    public function getYear(): string
    {
        return $this->year;
    }

    /**
     * @param string $year
     */
    public function setYear(string $year): void
    {
        $this->year = $year;
    }

    /**
     * @return AbstractCompetitionEntity
     */
    public function getCompetition(): AbstractCompetitionEntity
    {
        return $this->competition;
    }

    /**
     * @param AbstractCompetitionEntity $competition
     */
    public function setCompetition(AbstractCompetitionEntity $competition): void
    {
        $this->competition = $competition;
    }

    /**
     * @return LocationEntity
     */
    public function getLocation(): LocationEntity
    {
        return $this->location;
    }

    /**
     * @param LocationEntity $location
     */
    public function setLocation(LocationEntity $location): void
    {
        $this->location = $location;
    }

    /**
     * @return JudgeEntity[]|null
     */
    public function getJudges(): ?array
    {
        return $this->judges;
    }

    /**
     * @param JudgeEntity[]|null $judges
     */
    public function setJudges(?array $judges): void
    {
        $this->judges = $judges;
    }

    /**
     * @return bool
     */
    public function isUnusualTower(): bool
    {
        return $this->unusualTower;
    }

    /**
     * @param bool $unusualTower
     */
    public function setUnusualTower(bool $unusualTower): void
    {
        $this->unusualTower = $unusualTower;
    }

}
