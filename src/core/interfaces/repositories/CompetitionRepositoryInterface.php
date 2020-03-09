<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\DrlCompetitionEntity;

interface CompetitionRepositoryInterface
{
    const UNABLE_TO_INSERT_EXCEPTION = 2401;
    const NO_ROWS_FOUND_EXCEPTION = 2402;
    const NO_ROWS_UPDATED_EXCEPTION = 2403;
    const NO_ROWS_DELETED_EXCEPTION = 2404;

    public function insertDrlCompetition(
        DrlCompetitionEntity $entity
    ): DrlCompetitionEntity;

    public function selectDrlCompetition(int $id): DrlCompetitionEntity;

    /**
     * @param string $string
     * @return DrlCompetitionEntity[]
     */
    public function fuzzySearchDrlCompetition(string $string): array;


}