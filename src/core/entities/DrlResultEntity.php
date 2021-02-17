<?php
declare(strict_types=1);

namespace DrlArchive\core\entities;


class DrlResultEntity extends AbstractResultEntity
{
    private ?int $points;

    /**
     * @return int|null
     */
    public function getPoints(): ?int
    {
        return $this->points;
    }

    /**
     * @param int|null $points
     */
    public function setPoints(?int $points): void
    {
        $this->points = $points;
    }

}
