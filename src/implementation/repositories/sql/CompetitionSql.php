<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\AbstractCompetitionEntity;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\OtherCompetitionEntity;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\implementation\entities\DatabaseQueryBuilder;

class CompetitionSql extends MysqlRepository
    implements CompetitionRepositoryInterface
{
    // aliases
    public const FIELD_NAME_COMPETITION_ID = ' AS competitionId';
    public const FIELD_NAME_COMPETITION_NAME = ' AS competitionName';
    public const FIELD_NAME_IS_SINGLE_TOWER = ' AS isSingleTower';
    public const FIELD_NAME_IS_DRL_COMPETITION = ' AS isDrlCompetition';

    // fields
    public const SELECT_DRL_COMPETITION_ID = 'dc.id';
    public const SELECT_DRL_COMPETITION_NAME = 'dc.competitionName';
    public const SELECT_DRL_COMPETITION_SINGLE_TOWER = 'dc.isSingleTower';
    public const SELECT_OTHER_COMPETITION_ID = 'oc.id';
    public const SELECT_OTHER_COMPETITION_NAME = 'oc.competitionName';
    public const SELECT_OTHER_COMPETITION_SINGLE_TOWER = 'oc.isSingleTower';

    // tables and joins
    public const TABLE_DRL_COMPETITION = 'DRL_competition dc';
    public const TABLE_OTHER_COMPETITION = 'other_competition oc';
    public const INNER_JOIN_DRL_EVENT_ON_COMPETITION_ID_AND_LOCATION_ID = <<<join
INNER JOIN DRL_event de on dc.id = de.competitionID
AND de.locationID = :locationId
join;

    // where clauses
    public const WHERE_DRL_COMPETITION_NAME_LIKE =
        'dc.competitionName LIKE :search';
    public const WHERE_OTHER_COMPETITION_NAME_LIKE =
        'oc.competitionName LIKE :search';

    // order bys
    public const ORDER_BY_COMPETITION_NAME = 'competitionName';

    private function createDrlCompetitionEntity($result): DrlCompetitionEntity
    {
        $entity = new DrlCompetitionEntity();
        $entity->setId(
            (int)$result[substr(self::FIELD_NAME_COMPETITION_ID, 4)]
        );
        $entity->setName(
            $result[substr(self::FIELD_NAME_COMPETITION_NAME, 4)]
        );
        $entity->setSingleTowerCompetition(
            (bool)$result[substr(self::FIELD_NAME_IS_SINGLE_TOWER, 4)]
        );

        return $entity;
    }

    private function createOtherCompetitionEntity(
        array $result
    ): OtherCompetitionEntity {
        $entity = new OtherCompetitionEntity();
        $entity->setId(
            (int)$result[substr(self::FIELD_NAME_COMPETITION_ID, 4)]
        );
        $entity->setName(
            $result[substr(self::FIELD_NAME_COMPETITION_NAME, 4)]
        );
        $entity->setSingleTowerCompetition(
            (bool)$result[substr(self::FIELD_NAME_IS_SINGLE_TOWER, 4)]
        );
        return $entity;
    }

    public function insertDrlCompetition(
        DrlCompetitionEntity $entity
    ): DrlCompetitionEntity {
        // TODO: Implement insertCompetition() method.
    }

    public function selectDrlCompetition(int $id): DrlCompetitionEntity
    {
        // TODO: Implement selectCompetition() method.
    }

    /**
     * @inheritDoc
     * @throws GeneralRepositoryErrorException
     * @throws RepositoryNoResults
     */
    public function fuzzySearchDrlCompetition(string $string): array
    {
        $query = new DatabaseQueryBuilder();
        $query->setFields(
            $this->allDrlCompetitionFields()
        );
        $query->setTablesAndJoins([self::TABLE_DRL_COMPETITION]);
        $query->setWhereClauses([self::WHERE_DRL_COMPETITION_NAME_LIKE]);
        $query->setOrderBy([self::SELECT_DRL_COMPETITION_NAME]);

        $params = ['search' => "%{$string}%"];

        $results = $this->database->query(
            $this->buildSelectQuery($query),
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


    /**
     * @inheritDoc
     * @throws GeneralRepositoryErrorException
     * @throws RepositoryNoResults
     */
    public function fetchDrlCompetitionByLocationId(int $locationId): array
    {
        $query = new DatabaseQueryBuilder();
        $query->setFields(
            $this->allDrlCompetitionFields()
        );
        $query->setTablesAndJoins(
            [
                self::TABLE_DRL_COMPETITION,
                self::INNER_JOIN_DRL_EVENT_ON_COMPETITION_ID_AND_LOCATION_ID,
            ]
        );
        $query->setOrderBy(
            [
                self::SELECT_DRL_COMPETITION_NAME,
            ]
        );
        $query->isDistinctQuery();

        $params = [
            'locationId' => $locationId
        ];

        $results = $this->database->query(
            $this->buildSelectQuery($query),
            $params,
            Database::MULTI_ROW
        );

        if (empty($results)) {
            throw new RepositoryNoResults(
                'No competitions found',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        $returnArray = [];
        foreach ($results as $result) {
            $returnArray[] = $this->createDrlCompetitionEntity($result);
        }
        return $returnArray;
    }

    /**
     * @inheritDoc
     * @throws GeneralRepositoryErrorException
     * @throws RepositoryNoResults
     */
    public function fuzzySearchAllCompetitions(string $search): array
    {
        $drlQuery = new DatabaseQueryBuilder();
        $drlQuery->setFields(
            array_merge(
                $this->allDrlCompetitionFields(),
                ['1' . self::FIELD_NAME_IS_DRL_COMPETITION]
            )
        );
        $drlQuery->setTablesAndJoins([self::TABLE_DRL_COMPETITION]);
        $drlQuery->setWhereClauses([self::WHERE_DRL_COMPETITION_NAME_LIKE]);

        $otherQuery = new DatabaseQueryBuilder();
        $otherQuery->setFields(
            array_merge(
                $this->allOtherCompetitionFields(),
                [
                    '0' . self::FIELD_NAME_IS_DRL_COMPETITION
                ]
            )
        );
        $otherQuery->setTablesAndJoins([self::TABLE_OTHER_COMPETITION]);
        $otherQuery->setWhereClauses([self::WHERE_OTHER_COMPETITION_NAME_LIKE]);

        $params = ['search' => "%{$search}%"];

        $sql = $this->buildUnionSelectQuery(
            [
                $drlQuery,
                $otherQuery,
            ],
            [self::ORDER_BY_COMPETITION_NAME]
        );
        $results = $this->database->query(
            $sql,
            $params,
            Database::MULTI_ROW
        );
        if (empty($results)) {
            throw new RepositoryNoResults(
                'No results found',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        $competitionsArray = [];
        foreach ($results as $result) {
            if ((bool)$result['isDrlCompetition']) {
                $competitionsArray[] = $this->createDrlCompetitionEntity($result);
            } else {
                $competitionsArray[] = $this->createOtherCompetitionEntity($result);
            }
        }

        return $competitionsArray;
    }

    /**
     * @return string[]
     */
    private function allDrlCompetitionFields(): array
    {
        return [
            self::SELECT_DRL_COMPETITION_ID . self::FIELD_NAME_COMPETITION_ID,
            self::SELECT_DRL_COMPETITION_NAME . self::FIELD_NAME_COMPETITION_NAME,
            self::SELECT_DRL_COMPETITION_SINGLE_TOWER . self::FIELD_NAME_IS_SINGLE_TOWER,
        ];
    }

    private function allOtherCompetitionFields(): array
    {
        return [
            self::SELECT_OTHER_COMPETITION_ID . self::FIELD_NAME_COMPETITION_ID,
            self::SELECT_OTHER_COMPETITION_NAME . self::FIELD_NAME_COMPETITION_NAME,
            self::SELECT_OTHER_COMPETITION_SINGLE_TOWER . self::FIELD_NAME_IS_SINGLE_TOWER,
        ];
    }

}