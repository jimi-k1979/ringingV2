<?php

declare(strict_types=1);

namespace DrlArchive\core\entities;

class ChangeEntity extends Entity
{
    private int $changeNumber = 0;
    private int $upBell = 0;
    private int $downBell = 0;
    private int $bellToFollow = 0;

    /**
     * @return int
     */
    public function getChangeNumber(): int
    {
        return $this->changeNumber;
    }

    /**
     * @param int $changeNumber
     */
    public function setChangeNumber(int $changeNumber): void
    {
        $this->changeNumber = $changeNumber;
    }

    /**
     * @return int
     */
    public function getUpBell(): int
    {
        return $this->upBell;
    }

    /**
     * @param int $upBell
     */
    public function setUpBell(int $upBell): void
    {
        $this->upBell = $upBell;
    }

    /**
     * @return int
     */
    public function getDownBell(): int
    {
        return $this->downBell;
    }

    /**
     * @param int $downBell
     */
    public function setDownBell(int $downBell): void
    {
        $this->downBell = $downBell;
    }

    /**
     * @return int
     */
    public function getBellToFollow(): int
    {
        return $this->bellToFollow;
    }

    /**
     * @param int $bellToFollow
     */
    public function setBellToFollow(int $bellToFollow): void
    {
        $this->bellToFollow = $bellToFollow;
    }

}
