<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use _HumbugBox5d215ba2066e\Nette\NotImplementedException;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\core\interfaces\repositories\Repository;
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
    public const FIELD_NAME_ID = 'id';
    public const FIELD_NAME_EVENT_ID = 'eventId';
    public const FIELD_NAME_YEAR = 'year';
    public const FIELD_NAME_COMPETITION_ID = 'competitionId';
    public const FIELD_NAME_LOCATION_ID = 'locationId';
    public const FIELD_NAME_IS_UNUSUAL_TOWER = 'isUnusualTower';
    public const FIELD_NAME_USUAL_LOCATION = ' AS usualLocation';

    // tables and join
    public const TABLE_DRL_EVENT = 'DRL_event de';
    public const INNER_JOIN_DRL_COMPETITION =
        'INNER JOIN DRL_competition dc ON de.competitionID = dc.id';
    public const INNER_JOIN_DRL_COMPETITION_ON_ID_AND_NAME = <<<join
INNER JOIN DRL_competition dc ON de.competitionID = dc.id
AND dc.competitionName = :competitionName
join;
    public const LEFT_JOIN_LOCATION_ON_LOCATION_ID =
        'LEFT JOIN location l ON de.locationID = l.id';

    // where clauses
    public const WHERE_DRL_COMPETITION_ID_IS = 'de.competitionID = :competitionId';
    public const WHERE_DRL_EVENT_ID_IS = 'de.id = :eventId';
    public const WHERE_LOCATION_ID_IS = 'de.locationID = :locationId';
    public const WHERE_YEAR_IS = 'de.year = :year';

    public function insertDrlEvent(DrlEventEntity $entity): void
    {
        // TODO: Implement insertDrlEvent() method.
    }

    /**
     * @inheritDoc
     * @throws GeneralRepositoryErrorException
     * @throws RepositoryNoResultsException
     */
    public function fetchDrlEvent(int $id): DrlEventEntity
    {
        $query = new DatabaseQueryBuilder();
        $query->setFields(
            [
                self::SELECT_DRL_EVENT_ID . ' AS ' . self::FIELD_NAME_EVENT_ID,
                self::SELECT_DRL_EVENT_YEAR . ' AS ' . self::FIELD_NAME_YEAR,
                self::SELECT_DRL_EVENT_LOCATION_ID . ' AS ' . self::FIELD_NAME_LOCATION_ID,
                self::SELECT_DRL_EVENT_IS_UNUSUAL_TOWER . ' AS ' . self::FIELD_NAME_IS_UNUSUAL_TOWER,
                CompetitionSql::SELECT_DRL_COMPETITION_NAME . ' AS ' . CompetitionSql::FIELD_NAME_COMPETITION_NAME,
                CompetitionSql::SELECT_DRL_COMPETITION_SINGLE_TOWER . ' AS ' . CompetitionSql::FIELD_NAME_IS_SINGLE_TOWER,
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
            Database::FETCH_SINGLE_ROW
        );

        if (empty($result)) {
            throw new RepositoryNoResultsException(
                'No event found',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->createDrlEventEntity($result);
    }

    /**
     * @inheritDoc
     * @throws GeneralRepositoryErrorException
     * @throws RepositoryNoResultsException
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
            Database::FETCH_MULTI_ROW
        );

        if (empty($results)) {
            throw new RepositoryNoResultsException(
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

        if (isset($row[self::FIELD_NAME_ID])) {
            $entity->setId((int)$row[self::FIELD_NAME_ID]);
        } elseif (isset($row[self::FIELD_NAME_EVENT_ID])) {
            $entity->setId((int)$row[self::FIELD_NAME_EVENT_ID]);
        }

        if (isset($row[self::FIELD_NAME_YEAR])) {
            $entity->setYear($row[self::FIELD_NAME_YEAR]);
        }

        if (
            isset($row[self::FIELD_NAME_COMPETITION_ID]) ||
            isset($row[CompetitionSql::FIELD_NAME_COMPETITION_NAME]) ||
            isset($row[CompetitionSql::FIELD_NAME_IS_SINGLE_TOWER]) ||
            isset($row[substr(self::FIELD_NAME_USUAL_LOCATION, 4)])
        ) {
            $competition = new DrlCompetitionEntity();

            if (isset($row[self::FIELD_NAME_COMPETITION_ID])) {
                $competition->setId(
                    (int)$row[self::FIELD_NAME_COMPETITION_ID]
                );
            }

            if (isset($row[CompetitionSql::FIELD_NAME_COMPETITION_NAME])) {
                $competition->setName(
                    $row[CompetitionSql::FIELD_NAME_COMPETITION_NAME]
                );
            }
            if (isset($row[CompetitionSql::FIELD_NAME_IS_SINGLE_TOWER])) {
                $competition->setSingleTowerCompetition(
                    (bool)$row[CompetitionSql::FIELD_NAME_IS_SINGLE_TOWER]
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

        if (isset($row[self::FIELD_NAME_LOCATION_ID])) {
            $location = new LocationEntity();
            $location->setId((int)$row[self::FIELD_NAME_LOCATION_ID]);
            $entity->setLocation($location);
        }

        if (isset($row[self::FIELD_NAME_IS_UNUSUAL_TOWER])) {
            $entity->setUnusualTower(
                (bool)$row[self::FIELD_NAME_IS_UNUSUAL_TOWER]
            );
        }
        return $entity;
    }

    /**
     * @inheritDoc
     * @throws GeneralRepositoryErrorException
     * @throws RepositoryNoResultsException
     */
    public function fetchDrlEventsByCompetitionAndLocationIds(
        int $competitionId,
        int $locationId
    ): array {
        $query = new DatabaseQueryBuilder();
        $query->setFields(
            [
                self::SELECT_DRL_EVENT_ID . ' AS ' . self::FIELD_NAME_ID,
                self::SELECT_DRL_EVENT_YEAR . ' AS ' . self::FIELD_NAME_YEAR,
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
            Database::FETCH_MULTI_ROW
        );

        if (empty($results)) {
            throw new RepositoryNoResultsException(
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
     * @throws RepositoryNoResultsException
     */
    public function fetchDrlEventsByYear(string $year): array
    {
        $query = new DatabaseQueryBuilder();
        $query->setFields(
            [
                self::SELECT_DRL_EVENT_ID . ' AS ' . self::FIELD_NAME_ID,
                CompetitionSql::SELECT_DRL_COMPETITION_NAME . ' AS ' . CompetitionSql::FIELD_NAME_COMPETITION_NAME,
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
            Database::FETCH_MULTI_ROW
        );

        if (empty($results)) {
            throw new RepositoryNoResultsException(
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
                self::SELECT_DRL_EVENT_ID . ' AS ' . self::FIELD_NAME_EVENT_ID,
                self::SELECT_DRL_EVENT_YEAR . ' AS ' . self::FIELD_NAME_YEAR,
                self::SELECT_DRL_EVENT_IS_UNUSUAL_TOWER . ' AS ' . self::FIELD_NAME_IS_UNUSUAL_TOWER,
                self::SELECT_DRL_EVENT_COMPETITION_ID . ' AS ' . self::FIELD_NAME_COMPETITION_ID,
                CompetitionSql::SELECT_DRL_COMPETITION_NAME . ' AS ' . CompetitionSql::FIELD_NAME_COMPETITION_NAME,
                CompetitionSql::SELECT_DRL_COMPETITION_SINGLE_TOWER . ' AS ' . CompetitionSql::FIELD_NAME_IS_SINGLE_TOWER,
                CompetitionSql::SELECT_DRL_COMPETITION_USUAL_LOCATION_ID . ' AS ' . CompetitionSql::FIELD_NAME_USUAL_LOCATION_ID,
                self::SELECT_USUAL_LOCATION . ' AS ' . self::FIELD_NAME_USUAL_LOCATION,
                self::SELECT_DRL_EVENT_LOCATION_ID . ' AS ' . self::FIELD_NAME_LOCATION_ID,
                LocationSql::SELECT_LOCATION . ' AS ' . LocationSql::FIELD_NAME_LOCATION,
            ]
        );
        $query->setTablesAndJoins(
            [
                self::TABLE_DRL_EVENT,
                self::INNER_JOIN_DRL_COMPETITION_ON_ID_AND_NAME,
                CompetitionSql::LEFT_JOIN_DRL_COMPETITION_TO_USUAL_LOCATION,
                self::LEFT_JOIN_LOCATION_ON_LOCATION_ID,
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
            Database::FETCH_SINGLE_ROW
        );

        if (empty($result)) {
            throw new RepositoryNoResultsException(
                'No event found',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->createDrlEventEntity($result);
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventByYearAndCompetitionId(
        string $year,
        int $competitionId
    ): DrlEventEntity {
        throw new NotImplementedException(
            'Method not implemented - use EventDoctrine',
            Repository::METHOD_NOT_IMPLEMENTED_EXCEPTION
        );
    }
}
