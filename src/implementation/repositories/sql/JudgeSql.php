<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\entities\RingerEntity;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\JudgeRepositoryInterface;
use DrlArchive\implementation\entities\DatabaseQueryBuilder;

class JudgeSql extends MysqlRepository implements JudgeRepositoryInterface
{

    public const SELECT_ID = 'j.id';
    public const SELECT_FIRST_NAME = 'j.firstName';
    public const SELECT_LAST_NAME = 'j.lastName';
    public const SELECT_RINGER_ID = 'j.ringerID';

    public const FIELD_NAME_ID = 'id';
    public const FIELD_NAME_FIRST_NAME = 'firstName';
    public const FIELD_NAME_LAST_NAME = 'lastName';
    public const FIELD_NAME_RINGER_ID = 'ringerId';

    public const TABLE_JUDGE = 'judge j';
    public const INNER_JOIN_DRL_EVENT_JUDGE =
        'INNER JOIN DRL_event_judge dej ON j.id = dej.judgeID';

    public const WHERE_EVENT_ID_IS = 'dej.eventID = :eventId';

    /**
     * @param DrlEventEntity $entity
     * @return JudgeEntity[]
     * @throws RepositoryNoResultsException
     * @throws GeneralRepositoryErrorException
     */
    public function fetchJudgesByDrlEvent(DrlEventEntity $entity): array
    {
        $query = new DatabaseQueryBuilder();
        $query->setFields(
            [
                self::SELECT_ID . ' AS ' . self::FIELD_NAME_ID,
                self::SELECT_FIRST_NAME . ' AS ' . self::FIELD_NAME_FIRST_NAME,
                self::SELECT_LAST_NAME . ' AS ' . self::FIELD_NAME_LAST_NAME,
                self::SELECT_RINGER_ID . ' AS ' . self::FIELD_NAME_RINGER_ID,
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
            Database::FETCH_MULTI_ROW
        );

        if (empty($results)) {
            throw new RepositoryNoResultsException(
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
        $judge = new JudgeEntity();
        $judge->setId(
            (int)$result[self::FIELD_NAME_ID]
        );
        $judge->setFirstName(
            $result[self::FIELD_NAME_FIRST_NAME]
        );
        $judge->setLastName(
            $result[self::FIELD_NAME_LAST_NAME]
        );
        if ($result[self::FIELD_NAME_RINGER_ID] !== null) {
            $ringer = new RingerEntity();
            $ringer->setId(
                (int)$result[self::FIELD_NAME_ID]
            );
            $ringer->setFirstName(
                $result[self::FIELD_NAME_FIRST_NAME]
            );
            $ringer->setLastName(
                $result[self::FIELD_NAME_LAST_NAME]
            );
            $judge->setRinger($ringer);
        }

        return $judge;
    }
}