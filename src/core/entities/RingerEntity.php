<?php
declare(strict_types=1);

namespace DrlArchive\core\entities;


class RingerEntity extends Entity
{
    private ?string $firstName = null;
    private ?string $lastName = null;
    private ?string $notes = null;
    private ?int $judgeId = null;

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     */
    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    /**
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * @return int|null
     */
    public function getJudgeId(): ?int
    {
        return $this->judgeId;
    }

    /**
     * @param int|null $judgeId
     */
    public function setJudgeId(?int $judgeId): void
    {
        $this->judgeId = $judgeId;
    }

}
