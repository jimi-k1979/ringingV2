<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;

class CompositionEntity extends Entity
{
    private string $name = '';
    private int $numberOfBells = 0;
    private bool $tenorTurnedIn = false;
    private ?string $description = null;
    /**
     * @var ChangeEntity[]
     */
    private array $changes = [];

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
     * @return int
     */
    public function getNumberOfBells(): int
    {
        return $this->numberOfBells;
    }

    /**
     * @param int $numberOfBells
     */
    public function setNumberOfBells(int $numberOfBells): void
    {
        $this->numberOfBells = $numberOfBells;
    }

    /**
     * @return bool
     */
    public function isTenorTurnedIn(): bool
    {
        return $this->tenorTurnedIn;
    }

    /**
     * @param bool $tenorTurnedIn
     */
    public function setTenorTurnedIn(bool $tenorTurnedIn): void
    {
        $this->tenorTurnedIn = $tenorTurnedIn;
    }

    /**
     * @return ChangeEntity[]
     */
    public function getChanges(): array
    {
        return $this->changes;
    }

    /**
     * @param ChangeEntity[] $changes
     */
    public function setChanges(array $changes): void
    {
        $this->changes = $changes;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
