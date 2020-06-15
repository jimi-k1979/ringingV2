<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\sql;


use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;
use DrlArchive\implementation\entities\DatabaseQueryBuilder;

class LocationSql
    extends MysqlRepository
    implements LocationRepositoryInterface
{

    // fields
    public const SELECT_ID = 'l.id';
    public const SELECT_LOCATION = 'l.location';
    public const SELECT_DEANERY_ID = 'l.deaneryID';
    public const SELECT_DEDICATION = 'l.dedication';
    public const SELECT_TENOR_WEIGHT = 'l.tenorWeight';
    public const SELECT_NO_OF_BELLS = 'l.noOfBells';

    // aliases
    public const FIELD_NAME_ID = ' AS id';
    public const FIELD_NAME_LOCATION = ' AS location';
    public const FIELD_NAME_DEANERY_ID = ' AS deaneryId';
    public const FIELD_NAME_DEDICATION = ' AS dedication';
    public const FIELD_NAME_TENOR_WEIGHT = ' AS tenorWeight';
    public const FIELD_NAME_NO_OF_BELLS = ' AS noOfBells';

    // tables and joins
    public const TABLE_LOCATION = 'location l';

    // where clauses
    public const WHERE_ID_IS = 'l.id = :locationId';
    public const WHERE_LOCATION_LIKE = 'l.location LIKE (:search)';

    // order by

    public function insertLocation(LocationEntity $locationEntity): LocationEntity
    {
        // TODO: Implement createLocation() method.
    }

    public function selectLocation(int $locationId): LocationEntity
    {
        $query = new DatabaseQueryBuilder();
        $query->setFields(
            $this->allLocationFields()
        );
        $query->setTablesAndJoins(
            [
                self::TABLE_LOCATION,
            ]
        );
        $query->setWhereClauses(
            [
                self::WHERE_ID_IS
            ]
        );

        $params = [
            'locationId' => $locationId
        ];

        $result = $this->database->query(
            $this->buildSelectQuery($query),
            $params,
            Database::SINGLE_ROW
        );

        if (empty($result)) {
            throw new RepositoryNoResults(
                'No location found',
                LocationRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->createLocationEntity($result);
    }

    public function fuzzySearchLocation(string $search): array
    {
        $query = new DatabaseQueryBuilder();

        $query->setFields(
            $this->allLocationFields()
        );

        $query->setTablesAndJoins(
            [
                self::TABLE_LOCATION,
            ]
        );

        $query->setWhereClauses(
            [
                self::WHERE_LOCATION_LIKE,
            ]
        );

        $query->setOrderBy(
            [
                self::SELECT_LOCATION,
            ]
        );

        $params = [
            'search' => "%{$search}%",
        ];

        $results = $this->database->query(
            $this->buildSelectQuery($query),
            $params,
            Database::MULTI_ROW
        );

        if (empty($results)) {
            throw new RepositoryNoResults(
                'No locations found',
                LocationRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        $competitionsArray = [];
        foreach ($results as $result) {
            $competitionsArray[] = $this->createLocationEntity($result);
        }

        return $competitionsArray;
    }

    private function createLocationEntity(array $row): LocationEntity
    {
        $entity = new LocationEntity();

        if (isset($row[substr(self::FIELD_NAME_ID, 4)])) {
            $entity->setId((int)$row[substr(self::FIELD_NAME_ID, 4)]);
        }

        if (isset($row[substr(self::FIELD_NAME_LOCATION, 4)])) {
            $entity->setLocation($row[substr(self::FIELD_NAME_LOCATION, 4)]);
        }

        if (isset($row[substr(self::FIELD_NAME_DEANERY_ID, 4)])) {
            $deanery = new DeaneryEntity();
            $deanery->setId((int)$row[substr(self::FIELD_NAME_DEANERY_ID, 4)]);

            $entity->setDeanery($deanery);
        }

        if (isset($row[substr(self::FIELD_NAME_DEDICATION, 4)])) {
            $entity->setDedication(
                $row[substr(self::FIELD_NAME_DEDICATION, 4)]
            );
        }

        if (isset($row[substr(self::FIELD_NAME_TENOR_WEIGHT, 4)])) {
            $entity->setTenorWeight(
                $row[substr(self::FIELD_NAME_TENOR_WEIGHT, 4)]
            );
        }

        if (isset($row[substr(self::FIELD_NAME_NO_OF_BELLS, 4)])) {
            $entity->setNumberOfBells(
                $row[substr(self::FIELD_NAME_NO_OF_BELLS, 4)]
            );
        }
        return $entity;
    }

    /**
     * @return string[]
     */
    private function allLocationFields(): array
    {
        return [
            self::SELECT_ID . self::FIELD_NAME_ID,
            self::SELECT_LOCATION . self::FIELD_NAME_LOCATION,
            self::SELECT_DEANERY_ID . self::FIELD_NAME_DEANERY_ID,
            self::SELECT_DEDICATION . self::FIELD_NAME_DEDICATION,
            self::SELECT_TENOR_WEIGHT . self::FIELD_NAME_TENOR_WEIGHT,
            self::SELECT_NO_OF_BELLS . self::FIELD_NAME_NO_OF_BELLS,
        ];
    }
}