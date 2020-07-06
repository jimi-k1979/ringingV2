<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;

use DrlArchive\core\entities\LocationEntity;
use DrlArchive\implementation\repositories\sql\LocationSql;
use mocks\DatabaseMock;
use PHPUnit\Framework\TestCase;
use traits\StripStringTrait;

class LocationSqlTest extends TestCase
{
    use StripStringTrait;

    public function testSelectLocation(): void
    {
        $databaseMock = new DatabaseMock();
        $resultArray = [
            'id' => 1,
            'location' => 'Testville',
            'deaneryId' => 1,
            'dedication' => 'St Test',
            'tenorWeight' => '10 cwt',
            'noOfBells' => 6
        ];
        $databaseMock->addQueryResult(DatabaseMock::FIRST_CALL, $resultArray);

        $entity = (new LocationSql($databaseMock))->selectLocation(1);

        $queryArgs = $databaseMock->getQueryArgs();

        $expectedSql = <<<sql
SELECT
    l.id AS id,
    l.location AS location,
    l.deaneryID AS deaneryId,
    l.dedication AS dedication,
    l.tenorWeight AS tenorWeight,
    l.noOfBells AS noOfBells
FROM
    location l 
WHERE
    l.id = :locationId
sql;

        $this->assertEquals(
            $this->stripString($expectedSql),
            $this->stripString($queryArgs[DatabaseMock::FIRST_CALL]['sql']),
            'Incorrect sql'
        );

        $this->verifyLocationEntity($entity, $resultArray);
    }

    private function verifyLocationEntity(
        LocationEntity $entity,
        array $expected
    ): void {
        $this->assertEquals(
            $expected['id'],
            $entity->getId(),
            'Incorrect id'
        );
        $this->assertEquals(
            $expected['location'],
            $entity->getLocation(),
            'Incorrect location'
        );
        $this->assertEquals(
            $expected['deaneryId'],
            $entity->getDeanery()->getId(),
            'Incorrect deanery'
        );
        $this->assertEquals(
            $expected['dedication'],
            $entity->getDedication(),
            'Incorrect dedication'
        );
        $this->assertEquals(
            $expected['tenorWeight'],
            $entity->getTenorWeight(),
            'Incorrect tenor weight'
        );
        $this->assertEquals(
            $expected['noOfBells'],
            $entity->getNumberOfBells(),
            'Incorrect number of bells'
        );
    }

    public function testFuzzySearchLocation(): void
    {
        $databaseMock = new DatabaseMock();
        $resultArray = [
            [
                'id' => 1,
                'location' => 'Testville',
                'deaneryId' => 1,
                'dedication' => 'St Test',
                'tenorWeight' => '10 cwt',
                'noOfBells' => 6,
            ],
            [
                'id' => 2,
                'location' => 'Citesty',
                'deaneryId' => 4,
                'dedication' => 'St John',
                'tenorWeight' => '16-3-6',
                'noOfBells' => 10,
            ],
        ];
        $databaseMock->addQueryResult(DatabaseMock::FIRST_CALL, $resultArray);

        $entities = (new LocationSql($databaseMock))->fuzzySearchLocation('test');

        $queryArgs = $databaseMock->getQueryArgs();

        $expectedSql = <<<sql
SELECT
    l.id AS id,
    l.location AS location,
    l.deaneryID AS deaneryId,
    l.dedication AS dedication,
    l.tenorWeight AS tenorWeight,
    l.noOfBells AS noOfBells
FROM
    location l 
WHERE
    l.location LIKE (:search)
ORDER BY 
    l.location
sql;

        $this->assertEquals(
            $this->stripString($expectedSql),
            $this->stripString($queryArgs[DatabaseMock::FIRST_CALL]['sql']),
            'Incorrect sql'
        );

        foreach ($entities as $i => $entity) {
            $this->verifyLocationEntity($entity, $resultArray[$i]);
        }
    }
}
