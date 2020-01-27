<?php
declare(strict_types=1);

namespace DrlArchive\core\entities;


class JudgeEntity extends Entity
{
    /**
     * @var string
     */
    private $firstName;
    /**
     * @var string
     */
    private $lastName;
    /**
     * @var null|RingerEntity
     */
    private $ringer;

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return RingerEntity|null
     */
    public function getRinger(): ?RingerEntity
    {
        return $this->ringer;
    }

    /**
     * @param RingerEntity|null $ringer
     */
    public function setRinger(?RingerEntity $ringer): void
    {
        $this->ringer = $ringer;
    }

}