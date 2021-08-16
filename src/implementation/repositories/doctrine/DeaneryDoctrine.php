<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;


use Doctrine\DBAL\Query\QueryBuilder;
use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\Exceptions\repositories\RepositoryConnectionErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\DeaneryRepositoryInterface;
use DrlArchive\core\interfaces\repositories\Repository;
use Throwable;

class DeaneryDoctrine extends DoctrineRepository implements
    DeaneryRepositoryInterface
{

    private const FIELD_DEANERY_ID = 'd.id';
    private const FIELD_DEANERY_NAME = 'd.deaneryName';
    private const FIELD_DEANERY_REGION = 'd.northSouth';

    /**
     * @inheritDoc
     */
    public function selectDeanery(int $id): DeaneryEntity
    {
        try {
            $query = $this->baseSelectDeaneryFieldsQuery();
            $query->where(
                $query->expr()->eq(self::FIELD_DEANERY_ID, ':id')
            )
                ->setParameter('id', $id);

            $result = $query->executeQuery()->fetchAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No deanery found - connection error',
                DeaneryRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        if (empty($result)) {
            throw new RepositoryNoResultsException(
                'No deanery found',
                DeaneryRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateDeaneryEntity($result);
    }

    /**
     * @return QueryBuilder
     */
    private function baseSelectDeaneryFieldsQuery(): QueryBuilder
    {
        $queryBuilder = $this->database->createQueryBuilder();
        $queryBuilder->select(
            [
                self::FIELD_DEANERY_ID . ' AS ' . Repository::ALIAS_DEANERY_ID,
                self::FIELD_DEANERY_NAME . ' AS ' . Repository::ALIAS_DEANERY_NAME,
                self::FIELD_DEANERY_REGION . ' AS ' . Repository::ALIAS_DEANERY_REGION,
            ]
        )
            ->from('deanery', 'd');

        return $queryBuilder;
    }

    private function generateDeaneryEntity(array $row): DeaneryEntity
    {
        $entity = new DeaneryEntity();

        if (isset($row[Repository::ALIAS_DEANERY_ID])) {
            $entity->setId((int)$row[Repository::ALIAS_DEANERY_ID]);
        }
        if (isset($row[Repository::ALIAS_DEANERY_NAME])) {
            $entity->setName($row[Repository::ALIAS_DEANERY_NAME]);
        }
        if (isset($row[Repository::ALIAS_DEANERY_REGION])) {
            $entity->setRegion($row[Repository::ALIAS_DEANERY_REGION]);
        }

        return $entity;
    }

    public function getDeaneryByName(string $name): DeaneryEntity
    {
        try {
            $query = $this->baseSelectDeaneryFieldsQuery();
            $query->where(
                $query->expr()->eq(self::FIELD_DEANERY_NAME, ':name')
            )
                ->setParameter('name', $name);

            $result = $query->executeQuery()->fetchAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No deanery found - connection error',
                DeaneryRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        if (empty($result)) {
            throw new RepositoryNoResultsException(
                'No deanery found',
                DeaneryRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateDeaneryEntity($result);
    }
}
