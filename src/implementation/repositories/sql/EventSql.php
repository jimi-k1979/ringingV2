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
                self::SELECT_DRL_EVENT_ID . self::FIELD_ID,
                self::SELECT_DRL_EVENT_YEAR . self::FIELD_YEAR,
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

        if (isset($row['id'])) {
            $entity->setId((int)$row['id']);
        }

        if (isset($row['year'])) {
            $entity->setYear($row['year']);
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