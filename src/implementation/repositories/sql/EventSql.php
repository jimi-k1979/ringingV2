<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\implementation\entities\DatabaseQueryBuilder;

class EventSql extends MysqlRepository implements EventRepositoryInterface
{

    // fields
    const SELECT_DRL_EVENT_ID = 'de.id';
    const SELECT_DRL_EVENT_YEAR = 'de.year';

    // aliases
    const FIELD_NAME_ID = ' AS id';
    const FIELD_NAME_YEAR = ' AS year';

    // tables and join
    const TABLE_DRL_EVENT = 'DRL_event de';

    // where clauses
    const WHERE_DRL_COMPETITION_ID_IS = 'de.competitionID = :competitionId';

    public function insertDrlEvent(DrlEventEntity $entity): DrlEventEntity
    {
        // TODO: Implement insertDrlEvent() method.
    }

    public function fetchDrlEvent(int $id): DrlEventEntity
    {
        // TODO: Implement fetchDrlEvent() method.
    }


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
            $this->database->buildSelectQuery($query),
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
        }

        if (isset($row[substr(self::FIELD_NAME_YEAR, 4)])) {
            $entity->setYear($row[substr(self::FIELD_NAME_YEAR, 4)]);
        }

        if (isset($row['competitionID'])) {
            $competition = new DrlCompetitionEntity();
            $competition->setId((int)$row['competitionID']);
            $entity->setCompetition($competition);
        }

        if (isset($row['locationID'])) {
            $location = new LocationEntity();
            $location->setId((int)$row['locationID']);
            $entity->setLocation($location);
        }

        return $entity;
    }
}