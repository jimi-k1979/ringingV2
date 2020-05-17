<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\DrlResultEntity;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\ResultRepositoryInterface;
use DrlArchive\implementation\entities\DatabaseQueryBuilder;

class ResultSql extends MysqlRepository implements ResultRepositoryInterface
{

    public const SELECT_DRL_RESULT_ID = 'dr.id';
    public const SELECT_DRL_RESULT_POSITION = 'dr.position';
    public const SELECT_DRL_RESULT_PEAL_NUMBER = 'dr.pealNumber';
    public const SELECT_DRL_RESULT_FAULTS = 'dr.faults';
    public const SELECT_DRL_RESULT_POINTS = 'dr.points';
    public const FIELD_NAME_ID = ' AS id';
    public const FIELD_NAME_POSITION = ' AS position';
    public const FIELD_NAME_PEAL_NUMBER = ' AS pealNumber';
    public const FIELD_NAME_FAULTS = ' AS faults';
    public const FIELD_NAME_POINTS = ' AS points';
    public const TABLE_DRL_RESULT = 'DRL_result dr';
    public const INNER_JOIN_TEAM =
        'INNER JOIN team t ON dr.teamID = t.id';

    public const WHERE_DRL_RESULT_ID_IS = 'dr.id = :resultId';
    public const WHERE_DRL_RESULT_EVENT_ID_IS = 'dr.eventID = :eventId';

    public function insertDrlResult(
        DrlResultEntity $resultEntity
    ): DrlResultEntity {
        // TODO: Implement insertDrlResult() method.
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventResults(DrlEventEntity $eventEntity): array
    {
        $query = new DatabaseQueryBuilder();
        $query->setFields(
            [
                self::SELECT_DRL_RESULT_ID . self::FIELD_NAME_ID,
                self::SELECT_DRL_RESULT_POSITION . self::FIELD_NAME_POSITION,
                self::SELECT_DRL_RESULT_PEAL_NUMBER . self::FIELD_NAME_PEAL_NUMBER,
                self::SELECT_DRL_RESULT_FAULTS . self::FIELD_NAME_FAULTS,
                TeamSql::SELECT_TEAM_NAME . TeamSql::FIELD_NAME_TEAM_NAME,
                self::SELECT_DRL_RESULT_POINTS . self::FIELD_NAME_POINTS,
            ]
        );
        $query->setTablesAndJoins(
            [
                self::TABLE_DRL_RESULT,
                self::INNER_JOIN_TEAM,
            ]
        );
        $query->setWhereClauses(
            [
                self::WHERE_DRL_RESULT_EVENT_ID_IS,
            ]
        );
        $query->setOrderBy(
            [
                self::SELECT_DRL_RESULT_POSITION,
            ]
        );

        $params = [
            'eventId' => $eventEntity->getId()
        ];

        $results = $this->database->query(
            $this->database->buildSelectQuery($query),
            $params,
            Database::MULTI_ROW
        );

        if (empty($results)) {
            throw new RepositoryNoResults(
                'No results found',
                ResultRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        $returnArray = [];
        foreach ($results as $result) {
            $returnArray[] = $this->createDrlResultEntity($result);
        }

        return $returnArray;
    }

    private function createDrlResultEntity(array $row): DrlResultEntity
    {
        $entity = new DrlResultEntity();

        if (isset($row[substr(self::FIELD_NAME_ID, 4)])) {
            $entity->setId((int)$row[substr(self::FIELD_NAME_ID, 4)]);
        }

        if (isset($row[substr(self::FIELD_NAME_POSITION, 4)])) {
            $entity->setPosition((int)$row[substr(self::FIELD_NAME_POSITION, 4)]);
        }

        if (isset($row[substr(self::FIELD_NAME_PEAL_NUMBER, 4)])) {
            $entity->setPealNumber((int)$row[substr(self::FIELD_NAME_PEAL_NUMBER, 4)]);
        }

        if (isset($row[substr(self::FIELD_NAME_FAULTS, 4)])) {
            $entity->setFaults((float)$row[substr(self::FIELD_NAME_FAULTS, 4)]);
        }

        if (isset($row[substr(self::FIELD_NAME_POINTS, 4)])) {
            $entity->setPoints((int)$row[substr(self::FIELD_NAME_POINTS, 4)]);
        }

        if (
        isset($row[substr(TeamSql::FIELD_NAME_TEAM_NAME, 4)])
        ) {
            $team = new TeamEntity();
            $team->setName($row[substr(TeamSql::FIELD_NAME_TEAM_NAME, 4)]);

            $entity->setTeam($team);
        }

        return $entity;
    }
}