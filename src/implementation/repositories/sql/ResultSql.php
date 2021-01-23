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

    public const FIELD_NAME_ID = 'id';
    public const FIELD_NAME_POSITION = 'position';
    public const FIELD_NAME_PEAL_NUMBER = 'pealNumber';
    public const FIELD_NAME_FAULTS = 'faults';
    public const FIELD_NAME_POINTS = 'points';

    public const TABLE_DRL_RESULT = 'DRL_result dr';

    public const INNER_JOIN_TEAM =
        'INNER JOIN team t ON dr.teamID = t.id';

    public const WHERE_DRL_RESULT_ID_IS = 'dr.id = :resultId';
    public const WHERE_DRL_RESULT_EVENT_ID_IS = 'dr.eventID = :eventId';

    public function insertDrlResult(
        DrlResultEntity $resultEntity
    ): void {
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
                self::SELECT_DRL_RESULT_ID . ' AS ' . self::FIELD_NAME_ID,
                self::SELECT_DRL_RESULT_POSITION . ' AS ' . self::FIELD_NAME_POSITION,
                self::SELECT_DRL_RESULT_PEAL_NUMBER . ' AS ' . self::FIELD_NAME_PEAL_NUMBER,
                self::SELECT_DRL_RESULT_FAULTS . ' AS ' . self::FIELD_NAME_FAULTS,
                TeamSql::SELECT_TEAM_NAME . ' AS ' . TeamSql::FIELD_NAME_TEAM_NAME,
                self::SELECT_DRL_RESULT_POINTS . ' AS ' . self::FIELD_NAME_POINTS,
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
            $this->buildSelectQuery($query),
            $params,
            Database::FETCH_MULTI_ROW
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

        if (isset($row[self::FIELD_NAME_ID])) {
            $entity->setId((int)$row[self::FIELD_NAME_ID]);
        }

        if (isset($row[self::FIELD_NAME_POSITION])) {
            $entity->setPosition((int)$row[self::FIELD_NAME_POSITION]);
        }

        if (isset($row[self::FIELD_NAME_PEAL_NUMBER])) {
            $entity->setPealNumber((int)$row[self::FIELD_NAME_PEAL_NUMBER]);
        }

        if (isset($row[self::FIELD_NAME_FAULTS])) {
            $entity->setFaults((float)$row[self::FIELD_NAME_FAULTS]);
        }

        if (isset($row[self::FIELD_NAME_POINTS])) {
            $entity->setPoints((int)$row[self::FIELD_NAME_POINTS]);
        }

        if (
        isset($row[TeamSql::FIELD_NAME_TEAM_NAME])
        ) {
            $team = new TeamEntity();
            $team->setName($row[TeamSql::FIELD_NAME_TEAM_NAME]);

            $entity->setTeam($team);
        }

        return $entity;
    }
}
