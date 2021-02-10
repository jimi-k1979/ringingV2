<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\entities\OtherCompetitionEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\NotImplementedException;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\core\interfaces\repositories\Repository;
use DrlArchive\implementation\entities\DatabaseQueryBuilder;

class CompetitionSql extends MysqlRepository
    implements CompetitionRepositoryInterface
{
    // aliases
    public const FIELD_NAME_COMPETITION_ID = 'competitionId';
    public const FIELD_NAME_COMPETITION_NAME = 'competitionName';
    public const FIELD_NAME_IS_SINGLE_TOWER = 'isSingleTower';
    public const FIELD_NAME_IS_DRL_COMPETITION = 'isDrlCompetition';
    public const FIELD_NAME_USUAL_LOCATION_ID = 'usualLocationId';

    // fields
    public const SELECT_DRL_COMPETITION_ID = 'dc.id';
    public const SELECT_DRL_COMPETITION_NAME = 'dc.competitionName';
    public const SELECT_DRL_COMPETITION_SINGLE_TOWER = 'dc.isSingleTower';
    public const SELECT_OTHER_COMPETITION_ID = 'oc.id';
    public const SELECT_OTHER_COMPETITION_NAME = 'oc.competitionName';
    public const SELECT_OTHER_COMPETITION_SINGLE_TOWER = 'oc.isSingleTower';
    public const SELECT_DRL_COMPETITION_USUAL_LOCATION_ID = 'dc.usualLocationID';
    public const SELECT_OTHER_COMPETITION_USUAL_LOCATION_ID = 'oc.usualLocationID';

    // tables and joins
    public const TABLE_DRL_COMPETITION = 'DRL_competition dc';
    public const TABLE_OTHER_COMPETITION = 'other_competition oc';
    public const INNER_JOIN_DRL_EVENT_ON_COMPETITION_ID_AND_LOCATION_ID = <<<join
INNER JOIN DRL_event de ON dc.id = de.competitionID
AND de.locationID = :locationId
join;
    public const LEFT_JOIN_DRL_COMPETITION_TO_LOCATION =
        'LEFT JOIN location l ON dc.usualLocationID = l.id';
    public const LEFT_JOIN_OTHER_COMPETITION_TO_LOCATION =
        'LEFT JOIN location l ON oc.usualLocationID = l.id';
    public const LEFT_JOIN_DRL_COMPETITION_TO_USUAL_LOCATION =
        'LEFT JOIN location usualLocation ON dc.usualLocationID = usualLocation.id';

    // where clauses
    public const WHERE_DRL_COMPETITION_NAME_LIKE =
        'dc.competitionName LIKE :search';
    public const WHERE_OTHER_COMPETITION_NAME_LIKE =
        'oc.competitionName LIKE :search';
    public const WHERE_DRL_COMPETITION_ID_IS = 'dc.id = :id';
    public const WHERE_OTHER_COMPETITION_ID_IS = 'oc.id = :id';
    public const WHERE_DRL_COMPETITION_NAME_IS = 'dc.competitionName = :name';

    // order bys
    public const ORDER_BY_COMPETITION_NAME = 'competitionName';

    public function insertDrlCompetition(
        DrlCompetitionEntity $entity
    ): void {
        // TODO: Implement insertCompetition() method.
    }

    public function selectDrlCompetition(int $id): DrlCompetitionEntity
    {
        $query = new DatabaseQueryBuilder();
        $query->setFields(
            array_merge(
                $this->allDrlCompetitionFields(),
                [
                    LocationSql::SELECT_LOCATION . ' AS ' . LocationSql::FIELD_NAME_LOCATION,
                ]
            )
        );
        $query->setTablesAndJoins(
            [
                self::TABLE_DRL_COMPETITION,
                self::LEFT_JOIN_DRL_COMPETITION_TO_LOCATION,
            ]
        );
        $query->setWhereClauses([self::WHERE_DRL_COMPETITION_ID_IS]);

        $results = $this->database->query(
            $this->buildSelectQuery($query),
            ['id' => $id],
            Database::FETCH_SINGLE_ROW
        );

        if (empty($results)) {
            throw new RepositoryNoResultsException(
                'No competition found for that id',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->createDrlCompetitionEntity($results);
    }

    /**
     * @inheritDoc
     * @throws GeneralRepositoryErrorException
     * @throws RepositoryNoResultsException
     */
    public function fuzzySearchDrlCompetitions(string $string): array
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
            Database::FETCH_MULTI_ROW
        );

        if (empty($results)) {
            throw new RepositoryNoResultsException(
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
     * @throws RepositoryNoResultsException
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
            [self::SELECT_DRL_COMPETITION_NAME]
        );
        $query->setDistinctQuery();

        $params = [
            'locationId' => $locationId
        ];

        $results = $this->database->query(
            $this->buildSelectQuery($query),
            $params,
            Database::FETCH_MULTI_ROW
        );

        if (empty($results)) {
            throw new RepositoryNoResultsException(
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
     * @throws RepositoryNoResultsException
     */
    public function fuzzySearchAllCompetitions(string $search): array
    {
        $drlQuery = new DatabaseQueryBuilder();
        $drlQuery->setFields(
            array_merge(
                $this->allDrlCompetitionFields(),
                ['1 AS ' . self::FIELD_NAME_IS_DRL_COMPETITION]
            )
        );
        $drlQuery->setTablesAndJoins([self::TABLE_DRL_COMPETITION]);
        $drlQuery->setWhereClauses([self::WHERE_DRL_COMPETITION_NAME_LIKE]);

        $otherQuery = new DatabaseQueryBuilder();
        $otherQuery->setFields(
            array_merge(
                $this->allOtherCompetitionFields(),
                [
                    '0 AS ' . self::FIELD_NAME_IS_DRL_COMPETITION
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
            Database::FETCH_MULTI_ROW
        );
        if (empty($results)) {
            throw new RepositoryNoResultsException(
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

    private function createDrlCompetitionEntity(
        array $result
    ): DrlCompetitionEntity {
        $entity = new DrlCompetitionEntity();
        $entity->setId(
            (int)$result[self::FIELD_NAME_COMPETITION_ID]
        );
        $entity->setName(
            $result[self::FIELD_NAME_COMPETITION_NAME]
        );
        $entity->setSingleTowerCompetition(
            (bool)$result[self::FIELD_NAME_IS_SINGLE_TOWER]
        );
        $entity->setUsualLocation($this->createLocation($result));

        return $entity;
    }

    private function createOtherCompetitionEntity(
        array $result
    ): OtherCompetitionEntity {
        $entity = new OtherCompetitionEntity();
        $entity->setId(
            (int)$result[self::FIELD_NAME_COMPETITION_ID]
        );
        $entity->setName(
            $result[self::FIELD_NAME_COMPETITION_NAME]
        );
        $entity->setSingleTowerCompetition(
            (bool)$result[self::FIELD_NAME_IS_SINGLE_TOWER]
        );
        $entity->setUsualLocation($this->createLocation($result));

        return $entity;
    }

    private function createLocation(array $result): ?LocationEntity
    {
        if (
        !empty($result[self::FIELD_NAME_USUAL_LOCATION_ID])
        ) {
            $location = new LocationEntity();
            $location->setId(
                (int)$result[self::FIELD_NAME_USUAL_LOCATION_ID]
            );
            if (
            isset($result[LocationSql::FIELD_NAME_LOCATION])
            ) {
                $location->setLocation(
                    $result[LocationSql::FIELD_NAME_LOCATION]
                );
            }
        } else {
            $location = null;
        }

        return $location;
    }

    /**
     * @return string[]
     */
    private function allDrlCompetitionFields(): array
    {
        return [
            self::SELECT_DRL_COMPETITION_ID . ' AS ' . self::FIELD_NAME_COMPETITION_ID,
            self::SELECT_DRL_COMPETITION_NAME . ' AS ' . self::FIELD_NAME_COMPETITION_NAME,
            self::SELECT_DRL_COMPETITION_SINGLE_TOWER . ' AS ' . self::FIELD_NAME_IS_SINGLE_TOWER,
            self::SELECT_DRL_COMPETITION_USUAL_LOCATION_ID . ' AS ' . self::FIELD_NAME_USUAL_LOCATION_ID,
        ];
    }

    private function allOtherCompetitionFields(): array
    {
        return [
            self::SELECT_OTHER_COMPETITION_ID . ' AS ' . self::FIELD_NAME_COMPETITION_ID,
            self::SELECT_OTHER_COMPETITION_NAME . ' AS ' . self::FIELD_NAME_COMPETITION_NAME,
            self::SELECT_OTHER_COMPETITION_SINGLE_TOWER . ' AS ' . self::FIELD_NAME_IS_SINGLE_TOWER,
            self::SELECT_OTHER_COMPETITION_USUAL_LOCATION_ID . ' AS ' . self::FIELD_NAME_USUAL_LOCATION_ID,
        ];
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlCompetitionByName(
        string $competitionName
    ): DrlCompetitionEntity {
        $query = new DatabaseQueryBuilder();
        $query->setFields(
            array_merge(
                $this->allDrlCompetitionFields(),
                [
                    LocationSql::SELECT_LOCATION . ' AS ' . LocationSql::FIELD_NAME_LOCATION,
                ]
            )
        );
        $query->setTablesAndJoins(
            [
                self::TABLE_DRL_COMPETITION,
                self::LEFT_JOIN_DRL_COMPETITION_TO_LOCATION,
            ]
        );
        $query->setWhereClauses([self::WHERE_DRL_COMPETITION_NAME_IS]);

        $results = $this->database->query(
            $this->buildSelectQuery($query),
            ['name' => $competitionName],
            Database::FETCH_SINGLE_ROW
        );

        if (empty($results)) {
            throw new RepositoryNoResultsException(
                'No competition found for that id',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->createDrlCompetitionEntity($results);
    }

    /**
     * @inheritDoc
     * @throws CleanArchitectureException
     */
    public function fuzzySearchOtherCompetitions(string $search): array
    {
        throw new NotImplementedException(
            'Not implemented in this repository, use CompetitionDoctrine',
            Repository::METHOD_NOT_IMPLEMENTED_EXCEPTION
        );
    }
}
