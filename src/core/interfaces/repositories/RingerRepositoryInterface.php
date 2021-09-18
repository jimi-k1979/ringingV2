<?php

declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\RingerEntity;
use DrlArchive\core\entities\WinningRingerEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;

interface RingerRepositoryInterface
{
    public const UNABLE_TO_INSERT_EXCEPTION = 2901;
    public const NO_ROWS_FOUND_EXCEPTION = 2902;
    public const NO_ROWS_UPDATED_EXCEPTION = 2903;
    public const NO_ROWS_DELETED_EXCEPTION = 2904;

    /**
     * @param string $searchTerm
     * @return RingerEntity[]
     */
    public function fuzzySearchRinger(string $searchTerm): array;

    /**
     * @param DrlEventEntity $event
     * @return WinningRingerEntity[]
     */
    public function fetchWinningTeamByEvent(DrlEventEntity $event): array;

    /**
     * @param int $ringerId
     * @return RingerEntity
     * @throws CleanArchitectureException
     */
    public function fetchRingerById(int $ringerId): RingerEntity;

    /**
     * @param RingerEntity $ringer
     * @return WinningRingerEntity[]
     */
    public function fetchWinningRingerDetailsByRinger(
        RingerEntity $ringer
    ): array;
}
