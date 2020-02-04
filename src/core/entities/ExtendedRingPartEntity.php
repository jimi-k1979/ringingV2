<?php
declare(strict_types=1);

namespace DrlArchive\core\entities;


use DateTime;

class ExtendedRingPartEntity extends Entity
{

    /**
     * @var int
     */
    private $numberOfChanges;
    /**
     * @var int
     */
    private $partNumber;
    /**
     * @var DateTime
     */
    private $time;
    /**
     * @var string
     */
    private $name;

    /**
     * @return int
     */
    public function getNumberOfChanges(): int
    {
        return $this->numberOfChanges;
    }

    /**
     * @param int $numberOfChanges
     */
    public function setNumberOfChanges(int $numberOfChanges): void
    {
        $this->numberOfChanges = $numberOfChanges;
    }

    /**
     * @return int
     */
    public function getPartNumber(): int
    {
        return $this->partNumber;
    }

    /**
     * @param int $partNumber
     */
    public function setPartNumber(int $partNumber): void
    {
        $this->partNumber = $partNumber;
    }

    /**
     * @return DateTime
     */
    public function getTime(): DateTime
    {
        return $this->time;
    }

    /**
     * @param DateTime $time
     */
    public function setTime(DateTime $time): void
    {
        $this->time = $time;
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


}