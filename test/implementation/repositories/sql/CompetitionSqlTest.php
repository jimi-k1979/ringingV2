<?php

declare(strict_types=1);

namespace implementation\repositories\sql;

use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\implementation\repositories\sql\CompetitionSql;
use mocks\DatabaseMock;
use PHPUnit\Framework\TestCase;
use traits\StripStringTrait;

class CompetitionSqlTest extends TestCase
{
    use StripStringTrait;

    public function testFetchDrlCompetitionByLocationId()
    {
    }


    public function testInsertDrlCompetition()
    {
    }

    public function testFuzzySearchAllCompetitions()
    {
    }

    public function testSelectDrlCompetition()
    {
        $databaseMock = new DatabaseMock();
        $resultArray = [
            'competitionId' => 111,
            'competitionName' => 'Test competition',
            'isSingleTower' => 1,
            'usualLocationId' => 222,
            'location' => 'Testville, St Test',
        ];
        $databaseMock->addQueryResult(DatabaseMock::FIRST_CALL, $resultArray);

        $entity = (new CompetitionSql($databaseMock))
            ->selectDrlCompetition(111);

        $queryArgs = $databaseMock->getQueryArgs();

        $sql = <<<sql
SELECT
    dc.id AS competitionId,
    dc.competitionName AS competitionName,
    dc.isSingleTower AS isSingleTower,
    dc.usualLocationID AS usualLocationId,
    l.location AS location
FROM
     DRL_competition dc
     LEFT JOIN location l 
               ON dc.usualLocationID = l.id
WHERE
    dc.id = :id
sql;

        $this->assertEquals(
            $this->stripString($sql),
            $this->stripString($queryArgs[DatabaseMock::FIRST_CALL]['sql']),
            'Incorrect query'
        );

        $this->verifyDrlCompetitionEntity($entity, $resultArray);
    }

    private function verifyDrlCompetitionEntity(
        DrlCompetitionEntity $entity,
        array $resultArray
    ): void {
        $this->assertEquals(
            $resultArray['competitionId'],
            $entity->getId(),
            'Incorrect competition id returned'
        );
        $this->assertEquals(
            $resultArray['competitionName'],
            $entity->getName(),
            'Incorrect competition name returned'
        );
        $this->assertEquals(
            $resultArray['isSingleTower'],
            $entity->isSingleTowerCompetition(),
            'Incorrect single tower value returned'
        );
        $this->assertEquals(
            $resultArray['usualLocationId'],
            $entity->getUsualLocation()->getId(),
            'Incorrect usual location id returned'
        );
        $this->assertEquals(
            $resultArray['location'],
            $entity->getUsualLocation()->getLocation(),
            'Incorrect location returned'
        );
    }

    public function testFuzzySearchDrlCompetition()
    {
    }
}
