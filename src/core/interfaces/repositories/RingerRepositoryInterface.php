<?php

declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\RingerEntity;

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
}