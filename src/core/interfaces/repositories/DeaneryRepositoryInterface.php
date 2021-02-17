<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;

interface DeaneryRepositoryInterface
{
    public const UNABLE_TO_INSERT_EXCEPTION = 2001;
    public const NO_ROWS_FOUND_EXCEPTION = 2002;

    /**
     * @param int $id
     * @return DeaneryEntity
     * @throws CleanArchitectureException
     */
    public function selectDeanery(int $id): DeaneryEntity;

    /**
     * @param string $name
     * @return DeaneryEntity
     * @throws CleanArchitectureException
     */
    public function getDeaneryByName(string $name): DeaneryEntity;
}
