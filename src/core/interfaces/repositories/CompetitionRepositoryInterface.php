<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\CompetitionEntity;

interface CompetitionRepositoryInterface
{
    const UNABLE_TO_INSERT_EXCEPTION = 2401;
    const NO_ROWS_FOUND_EXCEPTION = 2402;
    const NO_ROWS_UPDATED = 2403;
    const NO_ROWS_DELETED = 2404;

    public function insertCompetition(
        CompetitionEntity $entity
    ): CompetitionEntity;

    public function selectCompetition(int $id): CompetitionEntity;
}