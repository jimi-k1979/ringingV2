<?php
declare(strict_types=1);

namespace DrlArchive\core\entities;


class ExtendedRingPartRingerEntity extends Entity
{
    private ExtendedRingPartEntity $extendedRingPart;
    private RingerEntity $ringer;
    private string $bell;
    private bool $conductor;

    /**
     * @return ExtendedRingPartEntity
     */
    public function getExtendedRingPart(): ExtendedRingPartEntity
    {
        return $this->extendedRingPart;
    }

    /**
     * @param ExtendedRingPartEntity $extendedRingPart
     */
    public function setExtendedRingPart(ExtendedRingPartEntity $extendedRingPart): void
    {
        $this->extendedRingPart = $extendedRingPart;
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
     * @return bool
     */
    public function isConductor(): bool
    {
        return $this->conductor;
    }

    /**
     * @param bool $conductor
     */
    public function setConductor(bool $conductor): void
    {
        $this->conductor = $conductor;
    }

}