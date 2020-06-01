<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\JudgeRepositoryInterface;
use DrlArchive\implementation\entities\DatabaseQueryBuilder;

class JudgeSql extends MysqlRepository implements JudgeRepositoryInterface
{

    public const SELECT_ID = 'j.id';
    public const SELECT_FIRST_NAME = 'j.firstName';
    public const SELECT_LAST_NAME = 'j.lastName';
    public const SELECT_RINGER_ID = 'j.ringerID';

    public const FIELD_NAME_ID = ' AS id';
    public const FIELD_NAME_FIRST_NAME = ' AS firstName';
    public const FIELD_NAME_LAST_NAME = ' AS lastName';
    public const FIELD_NAME_RINGER_ID = ' AS ringerId';

    public const TABLE_JUDGE = 'judge j';
    public const INNER_JOIN_DRL_EVENT_JUDGE =
        'INNER JOIN DRL_event_judge dej ON j.id = dej.judgeID';

    public const WHERE_EVENT_ID_IS = 'dej.eventID = :eventId';

    public function fetchJudgesByDrlEvent(DrlEventEntity $entity): array
    {
        $query = new DatabaseQueryBuilder();
        $query->setFields(
            [
                self::SELECT_ID . self::FIELD_NAME_ID,
                self::SELECT_FIRST_NAME . self::FIELD_NAME_FIRST_NAME,
                self::SELECT_LAST_NAME . self::FIELD_NAME_LAST_NAME,
                self::SELECT_RINGER_ID . self::FIELD_NAME_RINGER_ID,
            ]
        );
        $query->setTablesAndJoins(
            [
                self::TABLE_JUDGE,
                self::INNER_JOIN_DRL_EVENT_JUDGE,
            ]
        );
        $query->setWhereClauses(
            [
                self::WHERE_EVENT_ID_IS
            ]
        );
        $query->setOrderBy(
            [
                self::SELECT_LAST_NAME,
                self::SELECT_FIRST_NAME
            ]
        );

        $params = [
            'eventId' => $entity->getId()
        ];
        $results = $this->database->query(
            $this->buildSelectQuery($query),
            $params,
            Database::MULTI_ROW
        );

        if (empty($results)) {
            throw new RepositoryNoResults(
                'No judges found',
                JudgeRepositoryInterface::NO_RECORDS_FOUND_EXCEPTION
            );
        }

        $returnArray = [];
        foreach ($results as $result) {
            $returnArray[] = $this->createJudgeEntity($result);
        }

        return $returnArray;
    }

    private function createJudgeEntity(array $result): JudgeEntity
    {
    }
}