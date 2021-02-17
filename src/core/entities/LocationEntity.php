<?php
declare(strict_types=1);

namespace DrlArchive\core\entities;


class LocationEntity extends Entity
{
    private string $location;
    private DeaneryEntity $deanery;
    private string $dedication;
    private string $tenorWeight;
    private ?int $numberOfBells;

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location): void
    {
        $this->location = $location;
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
     * @return string
     */
    public function getDedication(): string
    {
        return $this->dedication;
    }

    /**
     * @param string $dedication
     */
    public function setDedication(string $dedication): void
    {
        $this->dedication = $dedication;
    }

    /**
     * @return string
     */
    public function getTenorWeight(): string
    {
        return $this->tenorWeight;
    }

    /**
     * @param string $tenorWeight
     */
    public function setTenorWeight(string $tenorWeight): void
    {
        $this->tenorWeight = $tenorWeight;
    }

    /**
     * @return null|int
     */
    public function getNumberOfBells(): ?int
    {
        return $this->numberOfBells;
    }

    /**
     * @param int|null $numberOfBells
     */
    public function setNumberOfBells(?int $numberOfBells): void
    {
        $this->numberOfBells = $numberOfBells;
    }
}