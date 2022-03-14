<?php

namespace DrlArchive\core\interfaces\repositories;

use DrlArchive\core\Exceptions\CleanArchitectureException;

interface PagesRepositoryInterface
{
    /**
     * @return array
     * @throws CleanArchitectureException
     */
    public function fetchRecordsPageList(): array;

}
