<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\DeaneryEntity;

interface DeaneryRepositoryInterface
{
    const UNABLE_TO_INSERT_EXCEPTION = 2001;
    const NO_ROWS_FOUND_EXCEPTION = 2002;

    public function selectDeanery(int $deaneryId): DeaneryEntity;

    public function getDeaneryByName(string $name): DeaneryEntity;
}