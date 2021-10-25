<?php

declare(strict_types=1);

namespace DrlArchive\mocks;

use DrlArchive\core\entities\CompositionEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interfaces\repositories\CompositionRepositoryInterface;
use DrlArchive\traits\CreateMockCompositionTrait;

class CompositionDummy implements CompositionRepositoryInterface
{
    use CreateMockCompositionTrait;

    /**
     * @inheritDoc
     */
    public function fetchAllCompositions(): array
    {
        return [$this->createMockComposition()];
    }
}
