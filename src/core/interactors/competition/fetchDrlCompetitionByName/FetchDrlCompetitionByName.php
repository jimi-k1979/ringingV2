<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\fetchDrlCompetitionByName;


use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;

class FetchDrlCompetitionByName extends Interactor
{
    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;

    public function execute(): void
    {
        // TODO: Implement execute() method.
    }

    public function setCompetitionRepository(
        CompetitionRepositoryInterface $repository
    ) {
        $this->competitionRepository = $repository;
    }
}
