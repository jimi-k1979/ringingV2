<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;

use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\implementation\repositories\sql\EventSql;
use DrlArchive\mocks\DatabaseMock;
use PHPUnit\Framework\TestCase;
use DrlArchive\traits\StripStringTrait;

class EventSqlTest extends TestCase
{
    use StripStringTrait;

    /**
     * @throws GeneralRepositoryErrorException
     * @throws RepositoryNoResults
     *
     */
    public function testFetchDrlEvent(): void
    {
        $databaseMock = new DatabaseMock();
        $resultArray = [
            'eventId' => '111',
            'year' => '1990',
            'locationId' => '1234',
            'isUsualTower' => '1',
            'competitionName' => 'Test competition',
            'isSingleTower' => '1',
        ];
        $databaseMock->addQueryResult(DatabaseMock::FIRST_CALL, $resultArray);

        $event = (new EventSql($databaseMock))
            ->fetchDrlEvent(111);

        $queryArgs = $databaseMock->getQueryArgs();

        $expectedSql = <<<sql
SELECT
    de.id AS eventId,
    de.year AS year,
    de.locationID AS locationId,
    de.isUnusualTower AS isUnusualTower,
    dc.competitionName AS competitionName,
    dc.isSingleTower AS isSingleTower
FROM
    DRL_event de
INNER JOIN DRL_competition dc ON de.competitionID = dc.id
WHERE
    de.id = :eventId
sql;

        $this->assertEquals(
            $this->stripString($expectedSql),
            $this->stripString($queryArgs[DatabaseMock::FIRST_CALL]['sql']),
            'Incorrect Sql'
        );

        $this->verifyDrlEventEntity($event, $resultArray);
    }

    private function verifyDrlEventEntity(
        DrlEventEntity $event,
        array $resultArray
    ): void {
        if (isset($resultArray['eventId'])) {
            $this->assertEquals(
                (int)$resultArray['eventId'],
                $event->getId(),
                'Incorrect event id'
            );
        }
        if (isset($resultArray['id'])) {
            $this->assertEquals(
                (int)$resultArray['id'],
                $event->getId(),
                'Incorrect event id'
            );
        }
        if (isset($resultArray['year'])) {
            $this->assertEquals(
                $resultArray['year'],
                $event->getYear(),
                'Incorrect event year'
            );
        }
        if (isset($resultArray['competitionId'])) {
            $this->assertEquals(
                (int)$resultArray['competitionId'],
                $event->getCompetition()->getId(),
                'Incorrect competition id'
            );
        }
        if (isset($resultArray['locationId'])) {
            $this->assertEquals(
                (int)$resultArray['locationId'],
                $event->getLocation()->getId(),
                'Incorrect location id'
            );
        }
        if (isset($resultArray['isUnusualTower'])) {
            $this->assertEquals(
                (bool)$resultArray['isUnusualTower'],
                $event->isUnusualTower(),
                'Incorrect unusual tower flag'
            );
        }

        if (isset($resultArray['competitionName'])) {
            $this->assertEquals(
                $resultArray['competitionName'],
                $event->getCompetition()->getName(),
                'Incorrect competition name'
            );
        }
        if (isset($resultArray['isSingleTower'])) {
            $this->assertEquals(
                (bool)$resultArray['isSingleTower'],
                $event->getCompetition()->isSingleTowerCompetition(),
                'Incorrect single tower flag'
            );
        }
        if (isset($resultArray['usualLocationId'])) {
            $this->assertEquals(
                (int)$resultArray['usualLocationId'],
                $event->getCompetition()->getUsualLocation()->getId(),
                'Incorrect usual location id'
            );
        }
        if (isset($resultArray['usualLocation'])) {
            $this->assertEquals(
                $resultArray['usualLocation'],
                $event->getCompetition()->getUsualLocation()->getLocation(),
                'Incorrect usual location name'
            );
        }

        if (isset($resultArray['location'])) {
            $this->assertEquals(
                $resultArray['location'],
                $event->getLocation()->getLocation(),
                'Incorrect location name'
            );
        }
    }

    /**
     * @throws GeneralRepositoryErrorException
     * @throws RepositoryNoResults
     */
    public function testFetchDrlEventsByYear(): void
    {
        $databaseMock = new DatabaseMock();
        $resultArray = [
            [
                'id' => '111',
                'competitionName' => 'Test competition',
            ],
            [
                'id' => '112',
                'competitionName' => 'Test event',
            ],
        ];
        $databaseMock->addQueryResult(DatabaseMock::FIRST_CALL, $resultArray);

        $events = (new EventSql($databaseMock))
            ->fetchDrlEventsByYear('1990');

        $queryArgs = $databaseMock->getQueryArgs();

        $expectedSql = <<<sql
SELECT
    de.id AS id,
    dc.competitionName AS competitionName
FROM
    DRL_event de
INNER JOIN DRL_competition dc ON de.competitionID = dc.id
WHERE
    de.year = :year
ORDER BY dc.competitionName
sql;

        $this->assertEquals(
            $this->stripString($expectedSql),
            $this->stripString($queryArgs[DatabaseMock::FIRST_CALL]['sql']),
            'Incorrect Sql'
        );

        foreach ($events as $i => $event) {
            $this->verifyDrlEventEntity($event, $resultArray[$i]);
        }
    }

    /**
     * @throws GeneralRepositoryErrorException
     * @throws RepositoryNoResults
     */
    public function testFetchDrlEventsByCompetitionAndLocationIds(): void
    {
        $databaseMock = new DatabaseMock();
        $resultArray = [
            [
                'id' => '111',
                'year' => '1990',
            ],
            [
                'id' => '112',
                'year' => '1991',
            ],
        ];
        $databaseMock->addQueryResult(DatabaseMock::FIRST_CALL, $resultArray);

        $events = (new EventSql($databaseMock))
            ->fetchDrlEventsByCompetitionAndLocationIds(1234, 111);

        $queryArgs = $databaseMock->getQueryArgs();

        $expectedSql = <<<sql
SELECT
    de.id AS id,
    de.year AS year
FROM
    DRL_event de
WHERE
    de.competitionID = :competitionId
    AND de.locationID = :locationId
ORDER BY de.year
sql;

        $this->assertEquals(
            $this->stripString($expectedSql),
            $this->stripString($queryArgs[DatabaseMock::FIRST_CALL]['sql']),
            'Incorrect Sql'
        );

        foreach ($events as $i => $event) {
            $this->verifyDrlEventEntity($event, $resultArray[$i]);
        }
    }

    /**
     * @throws GeneralRepositoryErrorException
     * @throws RepositoryNoResults
     */
    public function testFetchDrlEventsByCompetitionId(): void
    {
        $databaseMock = new DatabaseMock();
        $resultArray = [
            [
                'id' => '111',
                'year' => '1990',
            ],
            [
                'id' => '112',
                'year' => '1991',
            ],
        ];
        $databaseMock->addQueryResult(DatabaseMock::FIRST_CALL, $resultArray);

        $events = (new EventSql($databaseMock))
            ->fetchDrlEventsByCompetitionId(1234);

        $queryArgs = $databaseMock->getQueryArgs();

        $expectedSql = <<<sql
SELECT
    de.id AS id,
    de.year AS year
FROM
    DRL_event de
WHERE
    de.competitionID = :competitionId
ORDER BY de.year
sql;

        $this->assertEquals(
            $this->stripString($expectedSql),
            $this->stripString($queryArgs[DatabaseMock::FIRST_CALL]['sql']),
            'Incorrect Sql'
        );

        foreach ($events as $i => $event) {
            $this->verifyDrlEventEntity($event, $resultArray[$i]);
        }
    }

    /**
     * @throws RepositoryNoResults
     */
    public function testFetchDrlEventByYearAndCompetitionName(): void
    {
        $databaseMock = new DatabaseMock();
        $resultArray = [
            'eventId' => '111',
            'isSingleTower' => '1',
            'usualLocation' => 'Testville',
        ];
        $databaseMock->addQueryResult(DatabaseMock::FIRST_CALL, $resultArray);

        $event = (new EventSql($databaseMock))
            ->fetchDrlEventByYearAndCompetitionName(
                '1990',
                'Test competition'
            );

        $queryArgs = $databaseMock->getQueryArgs();

        $expectedSql = <<<sql
SELECT
    de.id AS eventId,
    de.year AS year,
    de.isUnusualTower AS isUnusualTower,
    de.competitionID AS competitionId,
    dc.competitionName AS competitionName,
    dc.isSingleTower AS isSingleTower,
    dc.usualLocationID AS usualLocationId,
    usualLocation.location AS usualLocation,
    de.locationID AS locationId,
    l.location AS location
FROM
    DRL_event de
    INNER JOIN DRL_competition dc 
        ON de.competitionID = dc.id
        AND dc.competitionName = :competitionName
    LEFT JOIN location usualLocation 
        ON dc.usualLocationID = usualLocation.id
    LEFT JOIN location l
        ON de.locationID = l.id
WHERE
    de.year = :year
sql;

        $this->assertEquals(
            $this->stripString($expectedSql),
            $this->stripString($queryArgs[DatabaseMock::FIRST_CALL]['sql']),
            'Incorrect Sql'
        );

        $this->verifyDrlEventEntity($event, $resultArray);
    }
}