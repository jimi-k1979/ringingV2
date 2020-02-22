<?php
declare(strict_types=1);

namespace DrlArchive\core\entities;


class EventEntity extends Entity
{
    /**
     * @var string
     */
    private $year;
    /**
     * @var AbstractCompetitionEntity
     */
    private $competition;
    /**
     * @var LocationEntity
     */
    private $location;
    /**
     * @var null|JudgeEntity[]
     */
    private $judges;

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

}