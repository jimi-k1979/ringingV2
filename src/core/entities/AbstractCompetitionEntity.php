<?php
declare(strict_types=1);

namespace DrlArchive\core\entities;


abstract class AbstractCompetitionEntity extends Entity
{
    public const COMPETITION_TYPE_OTHER = 0;
    public const COMPETITION_TYPE_DRL = 1;
    public const COMPETITION_TYPE_LADDER = 2;

    protected string $name;
    protected bool $singleTowerCompetition;
    protected ?LocationEntity $usualLocation = null;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isSingleTowerCompetition(): bool
    {
        return $this->singleTowerCompetition;
    }

    /**
     * @param bool $singleTowerCompetition
     */
    public function setSingleTowerCompetition(bool $singleTowerCompetition): void
    {
        $this->singleTowerCompetition = $singleTowerCompetition;
    }

    /**
     * @return null|LocationEntity
     */
    public function getUsualLocation(): ?LocationEntity
    {
        return $this->usualLocation;
    }

    /**
     * @param null|LocationEntity $usualLocation
     */
    public function setUsualLocation(?LocationEntity $usualLocation): void
    {
        $this->usualLocation = $usualLocation;
    }

    abstract public function toArray(): array;
}
