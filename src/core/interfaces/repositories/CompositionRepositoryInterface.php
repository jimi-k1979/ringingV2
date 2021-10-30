<?php

declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;

use DrlArchive\core\entities\CompositionEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;

interface CompositionRepositoryInterface
{
    public const NO_RESULTS_FOUND_EXCEPTION_CODE = 3002;

    /**
     * @return CompositionEntity[]
     * @throws CleanArchitectureException
     */
    public function fetchAllCompositions(): array;

    /**
     * @param int $id
     * @return CompositionEntity
     * @throws CleanArchitectureException
     */
    public function fetchCompositionById(int $id): CompositionEntity;

    /**
     * @param CompositionEntity $composition
     * @throws CleanArchitectureException
     */
    public function fetchChangesByComposition(CompositionEntity $composition): void;
}
