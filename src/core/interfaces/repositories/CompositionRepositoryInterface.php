<?php

declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;

use DrlArchive\core\entities\CompositionEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;

interface CompositionRepositoryInterface
{
    /**
     * @return CompositionEntity[]
     * @throws CleanArchitectureException
     */
    public function fetchAllCompositions(): array;
}
