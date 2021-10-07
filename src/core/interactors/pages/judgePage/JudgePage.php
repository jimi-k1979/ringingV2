<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\judgePage;

use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\JudgeRepositoryInterface;

class JudgePage extends Interactor
{

    private JudgeRepositoryInterface $judgeRepository;

    public function setJudgeRepository(
        JudgeRepositoryInterface $create
    ): void {
        $this->judgeRepository = $create;
    }

    public function execute(): void
    {
        // TODO: Implement execute() method.
    }
}
