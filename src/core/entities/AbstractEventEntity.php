<?php
declare(strict_types=1);

namespace DrlArchive\core\entities;


abstract class AbstractEventEntity extends Entity
{
    public const EVENT_TYPE_OTHER = 0;
    public const EVENT_TYPE_DRL = 1;
    public const EVENT_TYPE_LADDER = 2;

    private string $year = '';
    private ?AbstractCompetitionEntity $competition = null;
    private ?LocationEntity $location = null;
    /**
     * @var JudgeEntity[]
     */
    private array $judges = [];
    private bool $unusualTower = false;

    private ?float $totalFaults = null;
    private ?float $meanFaults = null;
    private ?float $winningMargin = null;

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
     * @return null|AbstractCompetitionEntity
     */
    public function getCompetition(): ?AbstractCompetitionEntity
    {
        return $this->competition;
    }

    /**
     * @param null|AbstractCompetitionEntity $competition
     */
    public function setCompetition(?AbstractCompetitionEntity $competition): void
    {
        $this->competition = $competition;
    }

    /**
     * @return null|LocationEntity
     */
    public function getLocation(): ?LocationEntity
    {
        return $this->location;
    }

    /**
     * @param null|LocationEntity $location
     */
    public function setLocation(?LocationEntity $location): void
    {
        $this->location = $location;
    }

    /**
     * @return JudgeEntity[]
     */
    public function getJudges(): array
    {
        return $this->judges;
    }

    /**
     * @param JudgeEntity[] $judges
     */
    public function setJudges(array $judges): void
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

    /**
     * @return float|null
     */
    public function getTotalFaults(): ?float
    {
        return $this->totalFaults;
    }

    /**
     * @param float|null $totalFaults
     */
    public function setTotalFaults(?float $totalFaults): void
    {
        $this->totalFaults = $totalFaults;
    }

    /**
     * @return float|null
     */
    public function getMeanFaults(): ?float
    {
        return $this->meanFaults;
    }

    /**
     * @param float|null $meanFaults
     */
    public function setMeanFaults(?float $meanFaults): void
    {
        $this->meanFaults = $meanFaults;
    }

    /**
     * @return float|null
     */
    public function getWinningMargin(): ?float
    {
        return $this->winningMargin;
    }

    /**
     * @param float|null $winningMargin
     */
    public function setWinningMargin(?float $winningMargin): void
    {
        $this->winningMargin = $winningMargin;
    }
    
}
