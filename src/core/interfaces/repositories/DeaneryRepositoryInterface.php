<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\DeaneryEntity;

interface DeaneryRepositoryInterface
{
    public const UNABLE_TO_INSERT_EXCEPTION = 2001;
    public const NO_ROWS_FOUND_EXCEPTION = 2002;

    public function selectDeanery(int $id): DeaneryEntity;

    public function getDeaneryByName(string $name): DeaneryEntity;
}
