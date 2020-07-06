<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;

use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\implementation\repositories\sql\JudgeSql;
use mocks\DatabaseMock;
use PHPUnit\Framework\TestCase;
use traits\CreateMockDrlEventTrait;
use traits\StripStringTrait;

class JudgeSqlTest extends TestCase
{
    use StripStringTrait;
    use CreateMockDrlEventTrait;

    public function testFetchJudgesByDrlEvent()
    {
        $databaseMock = new DatabaseMock();
        $resultArray = [
            [
                'id' => 111,
                'firstName' => 'Test',
                'lastName' => 'Judge',
                'ringerId' => 222,
            ],
            [
                'id' => 999,
                'firstName' => 'Test',
                'lastName' => 'Judges',
                'ringerId' => null,
            ],
        ];
        $databaseMock->addQueryResult(DatabaseMock::FIRST_CALL, $resultArray);
        $entities = (new JudgeSql($databaseMock))
            ->fetchJudgesByDrlEvent($this->createMockDrlEvent());

        $queryArgs = $databaseMock->getQueryArgs();

        $sql = <<<sql
SELECT
    j.id AS id,
    j.firstName AS firstName,
    j.lastName AS lastName,
    j.ringerID AS ringerId
FROM
    judge j
    INNER JOIN DRL_event_judge dej ON j.id = dej.judgeID
WHERE
    dej.eventID = :eventId
ORDER BY
    j.lastName, j.firstName
sql;

        $this->assertEquals(
            $this->stripString($sql),
            $this->stripString($queryArgs[DatabaseMock::FIRST_CALL]['sql']),
            'Incorrect query'
        );
    }

    private function verifyJudgeEntity(
        JudgeEntity $entity,
        array $resultArray
    ): void {
        $this->assertEquals(
            $resultArray['id'],
            $entity->getId(),
            'Incorrect judge id'
        );
        $this->assertEquals(
            $resultArray['firstName'],
            $entity->getFirstName(),
            'Incorrect first name'
        );
        $this->assertEquals(
            $resultArray['lastName'],
            $entity->getLastName(),
            'Incorrect last name'
        );
        if (empty($resultArray['ringerId'])) {
            $this->assertNull(
                $entity->getRinger(),
                'Incorrect ringer value'
            );
        } else {
            $this->assertEquals(
                $resultArray['ringerId'],
                $entity->getRinger()->getId(),
                'Incorrect ringer id'
            );
        }
    }
}
