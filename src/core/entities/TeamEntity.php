<?php
declare(strict_types=1);

namespace DrlArchive\core\entities;


class TeamEntity extends Entity
{
    private string $name;
    private DeaneryEntity $deanery;
    private ?int $earliestYear = null;
    private ?int $latestYear = null;

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
     * @return DeaneryEntity
     */
    public function getDeanery(): DeaneryEntity
    {
        return $this->deanery;
    }

    /**
     * @param DeaneryEntity $deanery
     */
    public function setDeanery(DeaneryEntity $deanery): void
    {
        $this->deanery = $deanery;
    }

    /**
     * @return int|null
     */
    public function getEarliestYear(): ?int
    {
        return $this->earliestYear;
    }

    /**
     * @param int|null $earliestYear
     */
    public function setEarliestYear(?int $earliestYear): void
    {
        $this->earliestYear = $earliestYear;
    }

    /**
     * @return int|null
     */
    public function getLatestYear(): ?int
    {
        return $this->latestYear;
    }

    /**
     * @param int|null $latestYear
     */
    public function setLatestYear(?int $latestYear): void
    {
        $this->latestYear = $latestYear;
    }

}
