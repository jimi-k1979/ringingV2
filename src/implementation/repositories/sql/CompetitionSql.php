<?php
declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\CompetitionEntity;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;

class CompetitionSql extends MysqlRepository implements CompetitionRepositoryInterface
{

    public function insertCompetition(CompetitionEntity $entity): CompetitionEntity
    {
        // TODO: Implement insertCompetition() method.
    }

    public function selectCompetition(int $id): CompetitionEntity
    {
        // TODO: Implement selectCompetition() method.
    }
}