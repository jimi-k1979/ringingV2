<?php
declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\implementation\entities\DatabaseQueryBuilder;

class CompetitionSql extends MysqlRepository implements CompetitionRepositoryInterface
{

    // fields
    public const SELECT_ID = 'dc.id';
    public const SELECT_COMPETITION_NAME = 'dc.competitionName';
    public const SELECT_IS_SINGLE_TOWER = 'dc.isSingleTower';

    // aliases
    public const FIELD_NAME_COMPETITION_ID = ' AS competitionId';
    public const FIELD_NAME_COMPETITION_NAME = ' AS competitionName';
    public const FIELD_NAME_IS_SINGLE_TOWER = ' AS isSingleTower';

    // tables and joins
    public const TABLE_DRL_COMPETITION = 'DRL_competition dc';

    // where clauses
    public const WHERE_NAME_LIKE = 'WHERE dc.competitionName LIKE :search';

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
        $query = new DatabaseQueryBuilder();

        $query->setFields(
            [
                self::SELECT_ID . self::FIELD_NAME_COMPETITION_ID,
                self::SELECT_COMPETITION_NAME . self::FIELD_NAME_COMPETITION_NAME,
                self::SELECT_IS_SINGLE_TOWER . self::FIELD_NAME_IS_SINGLE_TOWER,
            ]
        );

        $query->setTablesAndJoins(
            [
                self::TABLE_DRL_COMPETITION,
            ]
        );

        $query->setWhereClauses(
            [
                self::WHERE_NAME_LIKE,
            ]
        );

        $query->setOrderBy(
            [
                self::SELECT_COMPETITION_NAME,
            ]
        );

        $params = [
            'search' => "%{$string}%",
        ];

        $results = $this->database->query(
            $this->database->buildSelectQuery($query),
            $params,
            Database::MULTI_ROW
        );

        if (empty($results)) {
            throw new RepositoryNoResults(
                'No competitions found',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        $competitionsArray = [];
        foreach ($results as $result) {
            $competitionsArray[] = $this->createDrlCompetitionEntity($result);
        }

        return $competitionsArray;
    }

    private function createDrlCompetitionEntity($result): DrlCompetitionEntity
    {
        $entity = new DrlCompetitionEntity();
        $entity->setId(
            $result[substr(self::FIELD_NAME_COMPETITION_ID, 4)]
        );
        $entity->setName(
            $result[substr(self::FIELD_NAME_COMPETITION_NAME, 4)]
        );
        $entity->setSingleTowerCompetition(
            $result[substr(self::FIELD_NAME_IS_SINGLE_TOWER, 4)]
        );

        return $entity;
    }
}