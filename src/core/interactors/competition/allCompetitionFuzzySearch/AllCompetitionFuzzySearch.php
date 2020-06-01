<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\allCompetitionFuzzySearch;


use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;

class AllCompetitionFuzzySearch extends Interactor
{

    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;

    public function setCompetitionRepository(
        CompetitionRepositoryInterface $repository
    ): void {
        $this->competitionRepository = $repository;
    }

    public function execute(): void
    {
        // TODO: Implement execute() method.
    }

}