<?php
declare(strict_types=1);

namespace DrlArchive\core\entities;


use DateTime;

class ExtendedRingEntity extends Entity
{
    /**
     * @var LocationEntity
     */
    private $location;
    /**
     * @var DateTime
     */
    private $date;
    /**
     * @var string
     */
    private $footnote;
    /**
     * @var string
     */
    private $name;
    /**
     * @var ExtendedRingPartEntity[]
     */
    private $parts = [];
    /**
     * @var null|JudgeEntity[]
     */
    private $judges;

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
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getFootnote(): string
    {
        return $this->footnote;
    }

    /**
     * @param string $footnote
     */
    public function setFootnote(string $footnote): void
    {
        $this->footnote = $footnote;
    }

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
     * @return ExtendedRingPartEntity[]
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * @param ExtendedRingPartEntity[] $parts
     */
    public function setParts(array $parts): void
    {
        $this->parts = $parts;
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