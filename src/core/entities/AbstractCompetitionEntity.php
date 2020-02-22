<?php
declare(strict_types=1);

namespace DrlArchive\core\entities;


abstract class AbstractCompetitionEntity extends Entity
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var bool
     */
    private $singleTowerCompetition;

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

}