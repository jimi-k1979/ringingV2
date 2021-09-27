<?php
declare(strict_types=1);

namespace DrlArchive\core\entities;


class WinningRingerEntity extends Entity
{
    private ?string $bell = null;
    private ?RingerEntity $ringer = null;
    private ?DrlEventEntity $event = null;

    /**
     * @return string
     */
    public function getBell(): string
    {
        return $this->bell;
    }

    /**
     * @param string $bell
     */
    public function setBell(string $bell): void
    {
        $this->bell = $bell;
    }

    /**
     * @return RingerEntity
     */
    public function getRinger(): RingerEntity
    {
        return $this->ringer;
    }

    /**
     * @param RingerEntity $ringer
     */
    public function setRinger(RingerEntity $ringer): void
    {
        $this->ringer = $ringer;
    }

    /**
     * @return DrlEventEntity|null
     */
    public function getEvent(): ?DrlEventEntity
    {
        return $this->event;
    }

    /**
     * @param DrlEventEntity|null $event
     */
    public function setEvent(?DrlEventEntity $event): void
    {
        $this->event = $event;
    }

}
