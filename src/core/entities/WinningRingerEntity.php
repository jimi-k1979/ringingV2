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
     * @var AbstractEventEntity
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
     * @return AbstractEventEntity
     */
    public function getEvent(): AbstractEventEntity
    {
        return $this->event;
    }

    /**
     * @param AbstractEventEntity $event
     */
    public function setEvent(AbstractEventEntity $event): void
    {
        $this->event = $event;
    }

}