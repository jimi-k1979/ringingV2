<?php
declare(strict_types=1);

namespace DrlArchive\core\entities;


class WinningRingerEntity extends Entity
{
    /**
     * @var string
     */
    private $bell;
    /**
     * @var RingerEntity
     */
    private $ringer;
    /**
     * @var EventEntity
     */
    private $event;

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
     * @return EventEntity
     */
    public function getEvent(): EventEntity
    {
        return $this->event;
    }

    /**
     * @param EventEntity $event
     */
    public function setEvent(EventEntity $event): void
    {
        $this->event = $event;
    }

}