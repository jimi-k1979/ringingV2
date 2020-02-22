<?php
declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\interfaces\repositories\DrlCompetitionRepositoryInterface;

class DrlCompetitionSql extends MysqlRepository implements DrlCompetitionRepositoryInterface
{

    public function insertCompetition(DrlCompetitionEntity $entity): DrlCompetitionEntity
    {
        // TODO: Implement insertCompetition() method.
    }

    public function selectCompetition(int $id): DrlCompetitionEntity
    {
        // TODO: Implement selectCompetition() method.
    }
}