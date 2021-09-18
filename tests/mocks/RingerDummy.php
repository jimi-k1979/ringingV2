<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\RingerEntity;
use DrlArchive\core\entities\WinningRingerEntity;
use DrlArchive\core\interfaces\repositories\RingerRepositoryInterface;
use DrlArchive\traits\CreateMockRingerTrait;

class RingerDummy implements RingerRepositoryInterface
{
    use CreateMockRingerTrait;

    /**
     * @inheritDoc
     */
    public function fuzzySearchRinger(string $searchTerm): array
    {
        return [$this->createMockRinger()];
    }

    /**
     * @inheritDoc
     */
    public function fetchWinningTeamByEvent(DrlEventEntity $event): array
    {
        return [$this->createMockWinningRinger()];
    }

    /**
     * @inheritDoc
     */
    public function fetchRingerById(int $ringerId): RingerEntity
    {
        return $this->createMockRinger();
    }

    /**
     * @inheritDoc
     */
    public function fetchWinningRingerDetailsByRinger(RingerEntity $ringer): array
    {
        return [$this->createMockWinningRinger()];
    }
}
