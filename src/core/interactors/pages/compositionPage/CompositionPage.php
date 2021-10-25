<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\compositionPage;

use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompositionRepositoryInterface;

class CompositionPage extends Interactor
{

    private CompositionRepositoryInterface $compositionRepository;

    public function setCompositionRepository(
        CompositionRepositoryInterface $repository
    ): void {
        $this->compositionRepository = $repository;
    }

    public function execute(): void
    {
        // TODO: Implement execute() method.
    }
}
