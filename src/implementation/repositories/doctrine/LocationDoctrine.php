<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;


use Doctrine\DBAL\Query\QueryBuilder;
use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\Exceptions\InvalidEntityPropertyException;
use DrlArchive\core\Exceptions\repositories\RepositoryConnectionErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryInsertFailedException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;
use DrlArchive\core\interfaces\repositories\Repository;
use Throwable;

class LocationDoctrine extends DoctrineRepository implements
    LocationRepositoryInterface
{

    const FIELD_LOCATION_ID = 'l.id';
    const FIELD_LOCATION_NAME = 'l.location';
    const FIELD_LOCATION_DEANERY_ID = 'l.deaneryID';
    const FIELD_DEDICATION = 'l.dedication';
    const FIELD_TENOR_WEIGHT = 'l.tenorWeight';
    const FIELD_NUMBER_OF_BELLS = 'l.numberOfBells';
    const FIELD_DEANERY_NAME = 'd.deaneryName';
    const FIELD_DEANERY_REGION = 'd.deaneryRegion';

    /**
     * @inheritDoc
     */
    public function insertLocation(LocationEntity $location): void
    {
        try {
            $query = $this->database->createQueryBuilder();
            $query->insert('location')
                ->values(
                    [
                        'location' => ':location',
                        'deaneryID' => ':deaneryId',
                        'dedication' => ':dedication',
                        'tenorWeight' => ':weight',
                        'noOfBells' => ':bells',
                    ]
                )
                ->setParameters(
                    [
                        'location' => $location->getLocation(),
                        'deaneryId' => $location->getDeanery()->getId(),
                        'dedication' => $location->getDedication(),
                        'weight' => $location->getTenorWeight(),
                        'bells' => $location->getNumberOfBells(),
                    ]
                );
            $rows = $query->execute();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'Unable to insert location - connection error',
                LocationRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION
            );
        }

        if ($rows === 0) {
            throw new RepositoryInsertFailedException(
                'Unable to insert location',
                LocationRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION
            );
        }

        $location->setId((int)$this->database->getLastInsertId());
    }

    /**
     * @inheritDoc
     */
    public function fetchLocationById(int $locationId): LocationEntity
    {
        try {
            $query = $this->selectLocationBaseQuery();
            $query->where(
                $query->expr()->eq(self::FIELD_LOCATION_ID, ':id')
            )
                ->setParameter('id', $locationId);
            $result = $query->execute()->fetchAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No location found - connection error',
                LocationRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        if (empty($result)) {
            throw new RepositoryNoResultsException(
                'No location found',
                LocationRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateLocationEntity($result);
    }

    /**
     * @inheritDoc
     */
    public function fuzzySearchLocation(string $search): array
    {
        try {
            $query = $this->selectLocationBaseQuery();
            $query->where(
                $query->expr()->like(self::FIELD_LOCATION_NAME, ':search')
            )
                ->setParameter('search', "%{$search}%");
            $results = $query->execute()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No location found - connection error',
                LocationRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateLocationEntityArray($results);
    }

    /**
     * @inheritDoc
     */
    public function fetchLocationByName(string $name): LocationEntity
    {
        try {
            $query = $this->selectLocationBaseQuery();
            $query->where(
                $query->expr()->eq(self::FIELD_DEANERY_NAME, ':name')
            )
                ->setParameter('name', $name);
            $result = $query->execute()->fetchAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No location found - connection error',
                LocationRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        if (empty($result)) {
            throw new RepositoryNoResultsException(
                'No location found with that name',
                LocationRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateLocationEntity($result);
    }

    /**
     * @return QueryBuilder
     */
    private function selectLocationBaseQuery(): QueryBuilder
    {
        $queryBuilder = $this->database->createQueryBuilder();
        $queryBuilder->select(
            [
                self::FIELD_LOCATION_ID . ' AS ' . Repository::ALIAS_LOCATION_ID,
                self::FIELD_LOCATION_NAME . ' AS ' . Repository::ALIAS_LOCATION_NAME,
                self::FIELD_LOCATION_DEANERY_ID . ' AS ' . Repository::ALIAS_DEANERY_ID,
                self::FIELD_DEDICATION . ' AS ' . Repository::ALIAS_DEDICATION,
                self::FIELD_TENOR_WEIGHT . ' AS ' . Repository::ALIAS_TENOR_WEIGHT,
                self::FIELD_NUMBER_OF_BELLS . ' AS ' . Repository::ALIAS_NUMBER_OF_BELLS,
                self::FIELD_DEANERY_NAME . ' AS ' . Repository::ALIAS_DEANERY_NAME,
                self::FIELD_DEANERY_REGION . ' AS ' . Repository::ALIAS_DEANERY_REGION,
            ]
        )
            ->from('location', 'l')
            ->innerJoin(
                'l',
                'deanery',
                'd',
                'l.deaneryID = d.id'
            );

        return $queryBuilder;
    }

    /**
     * @param array $row
     * @return LocationEntity
     * @throws InvalidEntityPropertyException
     */
    private function generateLocationEntity(array $row): LocationEntity
    {
        $entity = new LocationEntity();
        $entity->setDeanery(new DeaneryEntity());

        if (isset($row[Repository::ALIAS_LOCATION_ID])) {
            $entity->setId((int)$row[Repository::ALIAS_LOCATION_ID]);
        }
        if (isset($row[Repository::ALIAS_LOCATION_NAME])) {
            $entity->setLocation($row[Repository::ALIAS_LOCATION_NAME]);
        }
        if (isset($row[Repository::ALIAS_DEANERY_ID])) {
            $entity->getDeanery()->setId(
                (int)$row[Repository::ALIAS_DEANERY_ID]
            );
        }
        if (isset($row[Repository::ALIAS_DEDICATION])) {
            $entity->setDedication($row[Repository::ALIAS_DEDICATION]);
        }
        if (isset($row[Repository::ALIAS_TENOR_WEIGHT])) {
            $entity->setTenorWeight($row[Repository::ALIAS_TENOR_WEIGHT]);
        }
        if (isset($row[Repository::ALIAS_NUMBER_OF_BELLS])) {
            $entity->setNumberOfBells((int)[Repository::ALIAS_NUMBER_OF_BELLS]);
        }
        if (isset($row[Repository::ALIAS_DEANERY_NAME])) {
            $entity->getDeanery()->setName(
                $row[Repository::ALIAS_DEANERY_NAME]
            );
        }
        if (isset($row[Repository::ALIAS_DEANERY_REGION])) {
            $entity->getDeanery()->setRegion(
                $row[Repository::ALIAS_DEANERY_REGION]
            );
        }

        return $entity;
    }

    /**
     * @param array $results
     * @return LocationEntity[]
     * @throws InvalidEntityPropertyException
     */
    private function generateLocationEntityArray(array $results): array
    {
        $returnArray = [];

        foreach ($results as $result) {
            $returnArray[] = $this->generateLocationEntity($result);
        }

        return $returnArray;
    }
}
