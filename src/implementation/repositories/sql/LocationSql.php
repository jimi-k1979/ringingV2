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
    public const FIELD_NAME_ID = 'id';
    public const FIELD_NAME_LOCATION = 'location';
    public const FIELD_NAME_DEANERY_ID = 'deaneryId';
    public const FIELD_NAME_DEDICATION = 'dedication';
    public const FIELD_NAME_TENOR_WEIGHT = 'tenorWeight';
    public const FIELD_NAME_NO_OF_BELLS = 'noOfBells';

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
            Database::FETCH_SINGLE_ROW
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
            Database::FETCH_MULTI_ROW
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

        if (isset($row[self::FIELD_NAME_ID])) {
            $entity->setId((int)$row[self::FIELD_NAME_ID]);
        }

        if (isset($row[self::FIELD_NAME_LOCATION])) {
            $entity->setLocation($row[self::FIELD_NAME_LOCATION]);
        }

        if (isset($row[self::FIELD_NAME_DEANERY_ID])) {
            $deanery = new DeaneryEntity();
            $deanery->setId((int)$row[self::FIELD_NAME_DEANERY_ID]);

            $entity->setDeanery($deanery);
        }

        if (isset($row[self::FIELD_NAME_DEDICATION])) {
            $entity->setDedication(
                $row[self::FIELD_NAME_DEDICATION]
            );
        }

        if (isset($row[self::FIELD_NAME_TENOR_WEIGHT])) {
            $entity->setTenorWeight(
                $row[self::FIELD_NAME_TENOR_WEIGHT]
            );
        }

        if (isset($row[self::FIELD_NAME_NO_OF_BELLS])) {
            $entity->setNumberOfBells(
                (int)$row[self::FIELD_NAME_NO_OF_BELLS]
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
            self::SELECT_ID . ' AS ' . self::FIELD_NAME_ID,
            self::SELECT_LOCATION . ' AS ' . self::FIELD_NAME_LOCATION,
            self::SELECT_DEANERY_ID . ' AS ' . self::FIELD_NAME_DEANERY_ID,
            self::SELECT_DEDICATION . ' AS ' . self::FIELD_NAME_DEDICATION,
            self::SELECT_TENOR_WEIGHT . ' AS ' . self::FIELD_NAME_TENOR_WEIGHT,
            self::SELECT_NO_OF_BELLS . ' AS ' . self::FIELD_NAME_NO_OF_BELLS,
        ];
    }
}