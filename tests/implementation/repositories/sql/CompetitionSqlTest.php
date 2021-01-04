<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;

use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\OtherCompetitionEntity;
use DrlArchive\mocks\DatabaseMock;
use PHPUnit\Framework\TestCase;
use DrlArchive\traits\StripStringTrait;

class CompetitionSqlTest extends TestCase
{
    use StripStringTrait;

    public function testFetchDrlCompetitionByLocationId(): void
    {
        $databaseMock = new DatabaseMock();
        $resultArray = [
            [
                'competitionId' => 111,
                'competitionName' => 'Test competition',
                'isSingleTower' => 1,
                'usualLocationId' => 222,
            ],
            [
                'competitionId' => 333,
                'competitionName' => 'Example competition',
                'isSingleTower' => 0,
                'usualLocationId' => null,

            ],
        ];
        $databaseMock->addQueryResult(DatabaseMock::FIRST_CALL, $resultArray);

        $entities = (new CompetitionSql($databaseMock))
            ->fetchDrlCompetitionByLocationId(222);

        $queryArgs = $databaseMock->getQueryArgs();

        $sql = <<<sql
SELECT DISTINCT
    dc.id AS competitionId,
    dc.competitionName AS competitionName,
    dc.isSingleTower AS isSingleTower,
    dc.usualLocationID AS usualLocationId
FROM
     DRL_competition dc
     INNER JOIN DRL_event de ON dc.id = de.competitionID
                AND de.locationID = :locationId 
ORDER BY dc.competitionName
sql;

        $this->assertEquals(
            $this->stripString($sql),
            $this->stripString($queryArgs[DatabaseMock::FIRST_CALL]['sql']),
            'Incorrect query'
        );

        foreach ($entities as $i => $entity) {
            $this->verifyDrlCompetitionEntity($entity, $resultArray[$i]);
        }
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
        if ($resultArray['usualLocationId'] !== null) {
            $this->assertEquals(
                $resultArray['usualLocationId'],
                $entity->getUsualLocation()->getId(),
                'Incorrect usual location id returned'
            );
        } else {
            $this->assertNull(
                $entity->getUsualLocation(),
                'Incorrect usual location return'
            );
        }
    }

    public function testFuzzySearchAllCompetitions(): void
    {
        $databaseMock = new DatabaseMock();
        $resultArray = [
            [
                'competitionId' => 111,
                'competitionName' => 'Test competition',
                'isSingleTower' => 1,
                'usualLocationId' => 222,
                'isDrlCompetition' => 1,
            ],
            [
                'competitionId' => 333,
                'competitionName' => 'Example competition',
                'isSingleTower' => 0,
                'usualLocationId' => null,
                'isDrlCompetition' => 0,
            ],
        ];
        $databaseMock->addQueryResult(DatabaseMock::FIRST_CALL, $resultArray);
        $entities = (new CompetitionSql($databaseMock))
            ->fuzzySearchAllCompetitions('competition');

        $queryArgs = $databaseMock->getQueryArgs();

        $sql = <<<sql
SELECT
    dc.id AS competitionId,
    dc.competitionName AS competitionName,
    dc.isSingleTower AS isSingleTower,
    dc.usualLocationID AS usualLocationId,
    1 AS isDrlCompetition
FROM
    DRL_competition dc
WHERE
    dc.competitionName LIKE :search
UNION
SELECT
    oc.id AS competitionId,
    oc.competitionName AS competitionName,
    oc.isSingleTower AS isSingleTower,
    oc.usualLocationID AS usualLocationId,
    0 AS isDrlCompetition
FROM
    other_competition oc 
WHERE
    oc.competitionName LIKE :search
ORDER BY competitionName
sql;

        $this->assertEquals(
            $this->stripString($sql),
            $this->stripString($queryArgs[DatabaseMock::FIRST_CALL]['sql']),
            'Incorrect query'
        );

        $this->verifyDrlCompetitionEntity($entities[0], $resultArray[0]);
        $this->verifyOtherCompetitionEntity($entities[1], $resultArray[1]);
    }

    private function verifyOtherCompetitionEntity(
        OtherCompetitionEntity $entity,
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
        if ($resultArray['usualLocationId'] !== null) {
            $this->assertEquals(
                $resultArray['usualLocationId'],
                $entity->getUsualLocation()->getId(),
                'Incorrect usual location id returned'
            );
        } else {
            $this->assertNull(
                $entity->getUsualLocation(),
                'Incorrect usual location return'
            );
        }
    }

    public function testSelectDrlCompetition(): void
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

    public function testFuzzySearchDrlCompetition(): void
    {
        $databaseMock = new DatabaseMock();
        $resultArray = [
            [
                'competitionId' => 111,
                'competitionName' => 'Test competition',
                'isSingleTower' => 1,
                'usualLocationId' => 222,
            ],
            [
                'competitionId' => 333,
                'competitionName' => 'Example competition',
                'isSingleTower' => 0,
                'usualLocationId' => null,

            ],
        ];
        $databaseMock->addQueryResult(DatabaseMock::FIRST_CALL, $resultArray);
        $entities = (new CompetitionSql($databaseMock))
            ->fuzzySearchDrlCompetitions('competition');

        $queryArgs = $databaseMock->getQueryArgs();

        $sql = <<<sql
SELECT
    dc.id AS competitionId,
    dc.competitionName AS competitionName,
    dc.isSingleTower AS isSingleTower,
    dc.usualLocationID AS usualLocationId
FROM
     DRL_competition dc
WHERE
    dc.competitionName LIKE :search
ORDER BY dc.competitionName
sql;

        $this->assertEquals(
            $this->stripString($sql),
            $this->stripString($queryArgs[DatabaseMock::FIRST_CALL]['sql']),
            'Incorrect query'
        );

        foreach ($entities as $i => $entity) {
            $this->verifyDrlCompetitionEntity($entity, $resultArray[$i]);
        }
    }

    public function testFetchDrlCompetitionByName(): void
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
            ->fetchDrlCompetitionByName('Test competition');

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
    dc.competitionName = :name
sql;

        $this->assertEquals(
            $this->stripString($sql),
            $this->stripString($queryArgs[DatabaseMock::FIRST_CALL]['sql']),
            'Incorrect query'
        );

        $this->verifyDrlCompetitionEntity($entity, $resultArray);
    }
}
