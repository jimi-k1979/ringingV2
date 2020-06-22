<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\implementation\entities\DatabaseQueryBuilder;

class EventSql extends MysqlRepository implements EventRepositoryInterface
{

    // fields
    public const SELECT_DRL_EVENT_ID = 'de.id';
    public const SELECT_DRL_EVENT_YEAR = 'de.year';
    public const SELECT_DRL_EVENT_COMPETITION_ID = 'de.competitionID';
    public const SELECT_DRL_EVENT_LOCATION_ID = 'de.locationID';
    public const SELECT_DRL_EVENT_IS_UNUSUAL_TOWER = 'de.isUnusualTower';
    public const SELECT_USUAL_LOCATION = 'usualLocation.location';

    // aliases
    public const FIELD_NAME_ID = ' AS id';
    public const FIELD_NAME_EVENT_ID = ' AS eventId';
    public const FIELD_NAME_YEAR = ' AS year';
    public const FIELD_NAME_COMPETITION_ID = ' AS competitionId';
    public const FIELD_NAME_LOCATION_ID = ' AS locationId';
    public const FIELD_NAME_IS_UNUSUAL_TOWER = ' AS isUnusualTower';
    public const FIELD_NAME_USUAL_LOCATION = ' AS usualLocation';

    // tables and join
    public const TABLE_DRL_EVENT = 'DRL_event de';
    public const INNER_JOIN_DRL_COMPETITION =
        'INNER JOIN DRL_competition dc ON de.competitionID = dc.id';
    public const INNER_JOIN_DRL_COMPETITION_ON_ID_AND_NAME = <<<join
INNER JOIN DRL_competition dc ON de.competitionID = dc.id
AND dc.competitionName = :competitionName
join;

    // where clauses
    public const WHERE_DRL_COMPETITION_ID_IS = 'de.competitionID = :competitionId';
    public const WHERE_DRL_EVENT_ID_IS = 'de.id = :eventId';
    public const WHERE_LOCATION_ID_IS = 'de.locationID = :locationId';
    public const WHERE_YEAR_IS = 'de.year = :year';

    public function insertDrlEvent(DrlEventEntity $entity): DrlEventEntity
    {
        // TODO: Implement insertDrlEvent() method.
    }

    /**
     * @inheritDoc
     * @throws GeneralRepositoryErrorException
     * @throws RepositoryNoResults
     */
    public function fetchDrlEvent(int $id): DrlEventEntity
    {
        $query = new DatabaseQueryBuilder();
        $query->setFields(
            [
                self::SELECT_DRL_EVENT_ID . self::FIELD_NAME_EVENT_ID,
                self::SELECT_DRL_EVENT_YEAR . self::FIELD_NAME_YEAR,
                self::SELECT_DRL_EVENT_LOCATION_ID . self::FIELD_NAME_LOCATION_ID,
                self::SELECT_DRL_EVENT_IS_UNUSUAL_TOWER . self::FIELD_NAME_IS_UNUSUAL_TOWER,
                CompetitionSql::SELECT_DRL_COMPETITION_NAME . CompetitionSql::FIELD_NAME_COMPETITION_NAME,
                CompetitionSql::SELECT_DRL_COMPETITION_SINGLE_TOWER . CompetitionSql::FIELD_NAME_IS_SINGLE_TOWER,
            ]
        );
        $query->setTablesAndJoins(
            [
                self::TABLE_DRL_EVENT,
                self::INNER_JOIN_DRL_COMPETITION,
            ]
        );
        $query->setWhereClauses(
            [
                self::WHERE_DRL_EVENT_ID_IS,
            ]
        );

        $params = [
            'eventId' => $id,
        ];

        $result = $this->database->query(
            $this->buildSelectQuery($query),
            $params,
            Database::SINGLE_ROW
        );

        if (empty($result)) {
            throw new RepositoryNoResults(
                'No event found',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->createDrlEventEntity($result);
    }

    /**
     * @inheritDoc
     * @throws GeneralRepositoryErrorException
     * @throws RepositoryNoResults
     */
    public function fetchDrlEventsByCompetitionId(int $competitionId): array
    {
        $query = new DatabaseQueryBuilder();
        $query->setFields(
            [
                self::SELECT_DRL_EVENT_ID . self::FIELD_NAME_ID,
                self::SELECT_DRL_EVENT_YEAR . self::FIELD_NAME_YEAR,
            ]
        );
        $query->setTablesAndJoins(
            [
                self::TABLE_DRL_EVENT,
            ]
        );
        $query->setWhereClauses(
            [
                self::WHERE_DRL_COMPETITION_ID_IS,
            ]
        );
        $query->setOrderBy(
            [
                self::SELECT_DRL_EVENT_YEAR,
            ]
        );

        $params = [
            'competitionId' => $competitionId,
        ];
        $results = $this->database->query(
            $this->buildSelectQuery($query),
            $params,
            Database::MULTI_ROW
        );

        if (empty($results)) {
            throw new RepositoryNoResults(
                'No events found',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        $returnArray = [];
        foreach ($results as $result) {
            $returnArray[] = $this->createDrlEventEntity($result);
        }
        return $returnArray;
    }

    private function createDrlEventEntity(array $row): DrlEventEntity
    {
        $entity = new DrlEventEntity();

        if (isset($row[substr(self::FIELD_NAME_ID, 4)])) {
            $entity->setId((int)$row[substr(self::FIELD_NAME_ID, 4)]);
        } elseif (isset($row[substr(self::FIELD_NAME_EVENT_ID, 4)])) {
            $entity->setId((int)$row[substr(self::FIELD_NAME_EVENT_ID, 4)]);
        }

        if (isset($row[substr(self::FIELD_NAME_YEAR, 4)])) {
            $entity->setYear($row[substr(self::FIELD_NAME_YEAR, 4)]);
        }

        if (
            isset($row[substr(self::FIELD_NAME_COMPETITION_ID, 4)]) ||
            isset($row[substr(CompetitionSql::FIELD_NAME_COMPETITION_NAME, 4)]) ||
            isset($row[substr(CompetitionSql::FIELD_NAME_IS_SINGLE_TOWER, 4)]) ||
            isset($row[substr(self::FIELD_NAME_USUAL_LOCATION, 4)])
        ) {
            $competition = new DrlCompetitionEntity();

            if (isset($row[substr(self::FIELD_NAME_COMPETITION_ID, 4)])) {
                $competition->setId(
                    (int)$row[substr(self::FIELD_NAME_COMPETITION_ID, 4)]
                );
            }

            if (isset($row[substr(CompetitionSql::FIELD_NAME_COMPETITION_NAME, 4)])) {
                $competition->setName(
                    $row[substr(CompetitionSql::FIELD_NAME_COMPETITION_NAME, 4)]
                );
            }
            if (isset($row[substr(CompetitionSql::FIELD_NAME_IS_SINGLE_TOWER, 4)])) {
                $competition->setSingleTowerCompetition(
                    (bool)$row[substr(CompetitionSql::FIELD_NAME_IS_SINGLE_TOWER, 4)]
                );
            }
            if (isset($row[substr(self::FIELD_NAME_USUAL_LOCATION, 4)])) {
                $location = new LocationEntity();
                $location->setLocation(
                    $row[substr(self::FIELD_NAME_USUAL_LOCATION, 4)]
                );
                $competition->setUsualLocation($location);
            }

            $entity->setCompetition($competition);
        }

        if (isset($row[substr(self::FIELD_NAME_LOCATION_ID, 4)])) {
            $location = new LocationEntity();
            $location->setId((int)$row[substr(self::FIELD_NAME_LOCATION_ID, 4)]);
            $entity->setLocation($location);
        }

        if (isset($row[substr(self::FIELD_NAME_IS_UNUSUAL_TOWER, 4)])) {
            $entity->setUnusualTower(
                (bool)$row[substr(self::FIELD_NAME_IS_UNUSUAL_TOWER, 4)]
            );
        }
        return $entity;
    }

    /**
     * @inheritDoc
     * @throws GeneralRepositoryErrorException
     * @throws RepositoryNoResults
     */
    public function fetchDrlEventsByCompetitionAndLocationIds(
        int $competitionId,
        int $locationId
    ): array {
        $query = new DatabaseQueryBuilder();
        $query->setFields(
            [
                self::SELECT_DRL_EVENT_ID . self::FIELD_NAME_ID,
                self::SELECT_DRL_EVENT_YEAR . self::FIELD_NAME_YEAR,
            ]
        );
        $query->setTablesAndJoins(
            [
                self::TABLE_DRL_EVENT,
            ]
        );
        $query->setWhereClauses(
            [
                self::WHERE_DRL_COMPETITION_ID_IS,
                self::WHERE_LOCATION_ID_IS,
            ]
        );
        $query->setOrderBy(
            [
                self::SELECT_DRL_EVENT_YEAR,
            ]
        );

        $params = [
            'competitionId' => $competitionId,
            'locationId' => $locationId,
        ];
        $results = $this->database->query(
            $this->buildSelectQuery($query),
            $params,
            Database::MULTI_ROW
        );

        if (empty($results)) {
            throw new RepositoryNoResults(
                'No events found',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        $returnArray = [];
        foreach ($results as $result) {
            $returnArray[] = $this->createDrlEventEntity($result);
        }
        return $returnArray;
    }

    /**
     * @inheritDoc
     * @throws GeneralRepositoryErrorException
     * @throws RepositoryNoResults
     */
    public function fetchDrlEventsByYear(string $year): array
    {
        $query = new DatabaseQueryBuilder();
        $query->setFields(
            [
                self::SELECT_DRL_EVENT_ID . self::FIELD_NAME_ID,
                CompetitionSql::SELECT_DRL_COMPETITION_NAME . CompetitionSql::FIELD_NAME_COMPETITION_NAME,
            ]
        );
        $query->setTablesAndJoins(
            [
                self::TABLE_DRL_EVENT,
                self::INNER_JOIN_DRL_COMPETITION,
            ]
        );
        $query->setWhereClauses(
            [
                self::WHERE_YEAR_IS,
            ]
        );
        $query->setOrderBy(
            [
                CompetitionSql::SELECT_DRL_COMPETITION_NAME,
            ]
        );

        $results = $this->database->query(
            $this->buildSelectQuery($query),
            ['year' => $year],
            Database::MULTI_ROW
        );

        if (empty($results)) {
            throw new RepositoryNoResults(
                'No events found',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        $returnArray = [];
        foreach ($results as $result) {
            $returnArray[] = $this->createDrlEventEntity($result);
        }

        return $returnArray;
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventByYearAndCompetitionName(
        string $year,
        string $competitionName
    ): DrlEventEntity {
        $query = new DatabaseQueryBuilder();

        $query->setFields(
            [
                self::SELECT_DRL_EVENT_ID . self::FIELD_NAME_EVENT_ID,
                CompetitionSql::SELECT_DRL_COMPETITION_SINGLE_TOWER . CompetitionSql::FIELD_NAME_IS_SINGLE_TOWER,
                self::SELECT_USUAL_LOCATION . self::FIELD_NAME_USUAL_LOCATION,
            ]
        );
        $query->setTablesAndJoins(
            [
                self::TABLE_DRL_EVENT,
                self::INNER_JOIN_DRL_COMPETITION_ON_ID_AND_NAME,
                CompetitionSql::LEFT_JOIN_DRL_COMPETITION_TO_USUAL_LOCATION,
            ]
        );
        $query->setWhereClauses([self::WHERE_YEAR_IS]);

        $params = [
            'competitionName' => $competitionName,
            'year' => $year,
        ];
        $result = $this->database->query(
            $this->buildSelectQuery($query),
            $params,
            Database::SINGLE_ROW
        );

        if (empty($result)) {
            throw new RepositoryNoResults(
                'No event found',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->createDrlEventEntity($result);
    }
}