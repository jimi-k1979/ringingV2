<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;


use Doctrine\DBAL\Query\QueryBuilder;
use DrlArchive\core\entities\AbstractCompetitionEntity;
use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\entities\OtherCompetitionEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\RepositoryConnectionErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryInsertFailedException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\core\interfaces\repositories\Repository;
use Throwable;

class CompetitionDoctrine extends DoctrineRepository implements
    CompetitionRepositoryInterface
{
    public const FIELD_DRL_COMPETITION_ID = 'dc.id';
    public const FIELD_DRL_COMPETITION_NAME = 'dc.competitionName';
    public const FIELD_DRL_SINGLE_TOWER = 'dc.isSingleTower';
    public const FIELD_DRL_USUAL_LOCATION_ID = 'dc.usualLocationID';
    public const FIELD_LOCATION = 'l.location';
    public const FIELD_DEDICATION = 'l.dedication';
    public const FIELD_TENOR_WEIGHT = 'l.tenorWeight';
    public const FIELD_NO_OF_BELLS = 'l.noOfBells';
    public const FIELD_DEANERY_ID = 'l.deaneryID';
    public const FIELD_DEANERY_NAME = 'd.deaneryName';
    public const FIELD_DEANERY_REGION = 'd.northSouth';
    public const FIELD_OTHER_COMPETITION_ID = 'oc.id';
    public const FIELD_OTHER_COMPETITION_NAME = 'oc.competitionName';
    public const FIELD_OTHER_SINGLE_TOWER = 'oc.isSingleTower';
    public const FIELD_OTHER_USUAL_LOCATION_ID = 'oc.usualLocationID';

    /**
     * @inheritDoc
     * @throws CleanArchitectureException
     */
    public function insertDrlCompetition(
        DrlCompetitionEntity $entity
    ): void {
        try {
            $queryBuilder = $this->database->createQueryBuilder();
            $queryBuilder->insert('DRL_competition')
                ->values(
                    [
                        'competitionName' => ':name',
                        'isSingleTower' => ':singleTower',
                        'usualLocationID' => ':location',
                    ]
                )
                ->setParameters(
                    [
                        'name' => $entity->getName(),
                        'singleTower' => $entity->isSingleTowerCompetition(),
                        'location' => $entity->getUsualLocation()->getId(),
                    ]
                );

            $rows = $queryBuilder->execute()->columnCount();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No competition inserted - connection error',
                CompetitionRepositoryInterface::NO_ROWS_CREATED_EXCEPTION
            );
        }
        if ($rows === 0) {
            throw new RepositoryInsertFailedException(
                'No competition inserted',
                CompetitionRepositoryInterface::NO_ROWS_CREATED_EXCEPTION
            );
        }

        $entity->setId((int)$this->database->getLastInsertId());
    }

    /**
     * @inheritDoc
     * @throws CleanArchitectureException
     */
    public function selectDrlCompetition(int $id): DrlCompetitionEntity
    {
        try {
            $query = $this->baseDrlCompetitionSelectQuery();
            $query->where(self::FIELD_DRL_COMPETITION_ID . ' = :id')
                ->setParameter('id', $id);

            $result = $query->execute()->fetchAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'Competition not found - connection error',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }
        if (empty($result)) {
            throw new RepositoryNoResultsException(
                'Competition not found',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateDrlCompetitionEntity($result);
    }

    /**
     * @return QueryBuilder
     */
    private function baseDrlCompetitionSelectQuery(): QueryBuilder
    {
        $queryBuilder = $this->database->createQueryBuilder();
        $queryBuilder->select(
            array_merge(
                $this->allDrlCompetitionFields(),
                $this->usualLocationFields()
            )
        )
            ->from('DRL_competition', 'dc')
            ->leftJoin(
                'dc',
                'location',
                'l',
                'dc.usualLocationID = l.id'
            )
            ->leftJoin(
                'dc',
                'deanery',
                'd',
                'l.deaneryID = d.id'
            );

        return $queryBuilder;
    }

    /**
     * @return string[]
     */
    private function allDrlCompetitionFields(): array
    {
        return [
            self::FIELD_DRL_COMPETITION_ID . ' AS ' . Repository::ALIAS_COMPETITION_ID,
            self::FIELD_DRL_COMPETITION_NAME . ' AS ' . Repository::ALIAS_COMPETITION_NAME,
            self::FIELD_DRL_SINGLE_TOWER . ' AS ' . Repository::ALIAS_IS_SINGLE_TOWER,
            self::FIELD_DRL_USUAL_LOCATION_ID . ' AS ' . Repository::ALIAS_USUAL_LOCATION_ID,
        ];
    }

    /**
     * @return string[]
     */
    private function usualLocationFields(): array
    {
        return [
            self::FIELD_LOCATION . ' AS ' . Repository::ALIAS_USUAL_LOCATION_NAME,
            self::FIELD_DEDICATION . ' AS ' . Repository::ALIAS_DEDICATION,
            self::FIELD_TENOR_WEIGHT . ' AS ' . Repository::ALIAS_TENOR_WEIGHT,
            self::FIELD_NO_OF_BELLS . ' AS ' . Repository::ALIAS_NUMBER_OF_BELLS,
            self::FIELD_DEANERY_ID . ' AS ' . Repository::ALIAS_DEANERY_ID,
            self::FIELD_DEANERY_NAME . ' AS ' . Repository::ALIAS_DEANERY_NAME,
            self::FIELD_DEANERY_REGION . ' AS ' . Repository::ALIAS_DEANERY_REGION,
        ];
    }

    /**
     * @param array $data
     * @return DrlCompetitionEntity
     * @throws CleanArchitectureException
     */
    private function generateDrlCompetitionEntity(
        array $data
    ): DrlCompetitionEntity {
        $entity = new DrlCompetitionEntity();
        $this->addDataToCompetitionArray($entity, $data);

        return $entity;
    }

    /**
     * @param AbstractCompetitionEntity $entity
     * @param array $data
     * @throws CleanArchitectureException
     */
    private function addDataToCompetitionArray(
        AbstractCompetitionEntity $entity,
        array $data
    ): void {
        $entity->setUsualLocation(new LocationEntity());
        $entity->getUsualLocation()->setDeanery(new DeaneryEntity());

        if (isset($data[Repository::ALIAS_COMPETITION_ID])) {
            $entity->setId((int)$data[Repository::ALIAS_COMPETITION_ID]);
        }
        if (isset($data[Repository::ALIAS_COMPETITION_NAME])) {
            $entity->setName($data[Repository::ALIAS_COMPETITION_NAME]);
        }
        if (isset($data[Repository::ALIAS_IS_SINGLE_TOWER])) {
            $entity->setSingleTowerCompetition(
                (bool)$data[Repository::ALIAS_IS_SINGLE_TOWER]
            );
        }
        if (isset($data[Repository::ALIAS_USUAL_LOCATION_ID])) {
            $entity->getUsualLocation()->setId(
                (int)$data[Repository::ALIAS_USUAL_LOCATION_ID]
            );
        }
        if (isset($data[Repository::ALIAS_USUAL_LOCATION_NAME])) {
            $entity->getUsualLocation()->setLocation(
                $data[Repository::ALIAS_USUAL_LOCATION_NAME]
            );
        }
        if (isset($data[Repository::ALIAS_DEDICATION])) {
            $entity->getUsualLocation()->setDedication(
                $data[Repository::ALIAS_DEDICATION]
            );
        }
        if (isset($data[Repository::ALIAS_TENOR_WEIGHT])) {
            $entity->getUsualLocation()->setTenorWeight(
                $data[Repository::ALIAS_TENOR_WEIGHT]
            );
        }
        if (isset($data[Repository::ALIAS_NUMBER_OF_BELLS])) {
            $entity->getUsualLocation()->setNumberOfBells(
                (int)$data[Repository::ALIAS_NUMBER_OF_BELLS]
            );
        }
        if (isset($data[Repository::ALIAS_DEANERY_ID])) {
            $entity->getUsualLocation()->getDeanery()->setId(
                (int)$data[Repository::ALIAS_DEANERY_ID]
            );
        }
        if (isset($data[Repository::ALIAS_DEANERY_NAME])) {
            $entity->getUsualLocation()->getDeanery()->setName(
                $data[Repository::ALIAS_DEANERY_NAME]
            );
        }
        if (isset($data[Repository::ALIAS_DEANERY_REGION])) {
            $entity->getUsualLocation()->getDeanery()->setRegion(
                $data[Repository::ALIAS_DEANERY_REGION]
            );
        }
    }

    /**
     * @inheritDoc
     * @throws CleanArchitectureException
     */
    public function fetchDrlCompetitionByLocationId(int $locationId): array
    {
        try {
            $query = $this->baseDrlCompetitionSelectQuery();
            $query->where(self::FIELD_DRL_USUAL_LOCATION_ID . ' = :locationId')
                ->setParameter('locationId', $locationId);

            $results = $query->execute()->fetchAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'Competition not found - connection error',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateDrlCompetitionEntityArray($results);
    }

    /**
     * @param array $data
     * @return DrlCompetitionEntity[]
     * @throws CleanArchitectureException
     */
    private function generateDrlCompetitionEntityArray(array $data): array
    {
        $entityArray = [];
        foreach ($data as $result) {
            $entityArray[] = $this->generateDrlCompetitionEntity($result);
        }

        return $entityArray;
    }

    /**
     * @inheritDoc
     * @throws CleanArchitectureException
     */
    public function fuzzySearchAllCompetitions(string $search): array
    {
        return array_merge(
            $this->fuzzySearchDrlCompetitions($search),
            $this->fuzzySearchOtherCompetitions($search)
        );
    }

    /**
     * @inheritDoc
     * @throws CleanArchitectureException
     */
    public function fuzzySearchDrlCompetitions(string $string): array
    {
        try {
            $query = $this->baseDrlCompetitionSelectQuery();
            $query->where(
                $query->expr()->like(
                    self::FIELD_DRL_COMPETITION_NAME,
                    ':string'
                )
            )
                ->setParameter('string', "%{$string}%");

            $results = $query->execute()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'Competition not found - connection error',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateDrlCompetitionEntityArray($results);
    }

    /**
     * @inheritDoc
     * @throws CleanArchitectureException
     */
    public function fuzzySearchOtherCompetitions(string $search): array
    {
        try {
            $query = $this->baseOtherCompetitionSelectQuery();
            $query->where(
                $query->expr()->like(
                    self::FIELD_OTHER_COMPETITION_NAME,
                    ':string'
                )
            )
                ->setParameter('string', "%{$search}%");

            $results = $query->execute()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'Competition not found - connection error',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateOtherCompetitionEntityArray($results);
    }

    /**
     * @return QueryBuilder
     */
    private function baseOtherCompetitionSelectQuery(): QueryBuilder
    {
        $queryBuilder = $this->database->createQueryBuilder();
        $queryBuilder->select(
            array_merge(
                $this->allOtherCompetitionFields(),
                $this->usualLocationFields()
            )
        )
            ->from('other_competition', 'oc')
            ->innerJoin(
                'oc',
                'location',
                'l',
                'oc.usualLocationID = l.id'
            )
            ->innerJoin(
                'oc',
                'deanery',
                'd',
                'l.deaneryID = d.id'
            );

        return $queryBuilder;
    }

    /**
     * @return string[]
     */
    private function allOtherCompetitionFields(): array
    {
        return [
            self::FIELD_OTHER_COMPETITION_ID . ' AS ' . Repository::ALIAS_COMPETITION_ID,
            self::FIELD_OTHER_COMPETITION_NAME . ' AS ' . Repository::ALIAS_COMPETITION_NAME,
            self::FIELD_OTHER_SINGLE_TOWER . ' AS ' . Repository::ALIAS_IS_SINGLE_TOWER,
            self::FIELD_OTHER_USUAL_LOCATION_ID . ' AS ' . Repository::ALIAS_USUAL_LOCATION_ID,
        ];
    }

    /**
     * @param array $data
     * @return OtherCompetitionEntity[]
     * @throws CleanArchitectureException
     */
    private function generateOtherCompetitionEntityArray(array $data): array
    {
        $entityArray = [];
        foreach ($data as $result) {
            $entityArray[] = $this->generateOtherCompetitionEntity($result);
        }

        return $entityArray;
    }

    /**
     * @param array $data
     * @return OtherCompetitionEntity
     * @throws CleanArchitectureException
     */
    private function generateOtherCompetitionEntity(
        array $data
    ): OtherCompetitionEntity {
        $entity = new OtherCompetitionEntity();
        $this->addDataToCompetitionArray($entity, $data);

        return $entity;
    }

    /**
     * @inheritDoc
     * @throws CleanArchitectureException
     */
    public function fetchDrlCompetitionByName(
        string $competitionName
    ): DrlCompetitionEntity {
        try {
            $query = $this->baseDrlCompetitionSelectQuery();
            $query->where(self::FIELD_DRL_COMPETITION_NAME . ' = :name')
                ->setParameter('name', $competitionName);

            $result = $query->execute()->fetchAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'Competition not found - connection error',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }
        if (empty($result)) {
            throw new RepositoryNoResultsException(
                'Competition not found',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateDrlCompetitionEntity($result);
    }
}
