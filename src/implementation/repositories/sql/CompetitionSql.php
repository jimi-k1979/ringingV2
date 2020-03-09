<?php
declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;

class CompetitionSql extends MysqlRepository implements CompetitionRepositoryInterface
{

    public function insertDrlCompetition(DrlCompetitionEntity $entity): DrlCompetitionEntity
    {
        // TODO: Implement insertCompetition() method.
    }

    public function selectDrlCompetition(int $id): DrlCompetitionEntity
    {
        // TODO: Implement selectCompetition() method.
    }

    /**
     * @inheritDoc
     */
    public function fuzzySearchDrlCompetition(string $string): array
    {
        // TODO: Implement fuzzySearchDrlCompetition() method.
    }
}