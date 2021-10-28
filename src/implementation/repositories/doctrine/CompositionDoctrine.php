<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;

use Doctrine\DBAL\Query\QueryBuilder;
use DrlArchive\core\entities\CompositionEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\RepositoryConnectionErrorException;
use DrlArchive\core\interfaces\repositories\CompositionRepositoryInterface;
use DrlArchive\core\interfaces\repositories\Repository;

class CompositionDoctrine extends DoctrineRepository implements
    CompositionRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function fetchAllCompositions(): array
    {
        try {
            $query = $this->baseFetchAllCompositionFieldsSelect();
            $results = $query->executeQuery()->fetchAllAssociative();
        } catch (\Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No compositions found - connection error',
                Repository::REPOSITORY_ERROR_CONNECTION
            );
        }

        return $this->generateCompositionEntityArray($results);
    }

    private function baseFetchAllCompositionFieldsSelect(): QueryBuilder
    {
        $query = $this->database->createQueryBuilder();
        $query->select(
            'c.id AS ' . Repository::ALIAS_COMPOSITION_ID,
            'c.compositionName AS ' . Repository::ALIAS_COMPOSITION,
            'c.numberOfBells AS ' . Repository::ALIAS_NUMBER_OF_BELLS,
            'c.tenorTurnedIn AS ' . Repository::ALIAS_TENOR_TURNED_IN,
        )
            ->from(
                'composition',
                'c'
            );

        return $query;
    }

    /**
     * @param array $results
     * @return CompositionEntity[]
     */
    private function generateCompositionEntityArray(array $results): array
    {
        $returnArray = [];
        foreach ($results as $result) {
            $returnArray[] = $this->generateCompositionEntity($result);
        }
        return $returnArray;
    }

    /**
     * @param array $result
     * @return CompositionEntity
     */
    private function generateCompositionEntity(
        array $result
    ): CompositionEntity {
        $entity = new CompositionEntity();
        if (isset($result[Repository::ALIAS_COMPOSITION_ID])) {
            $entity->setId(
                (int)$result[Repository::ALIAS_COMPOSITION_ID]
            );
        }
        if (isset($result[Repository::ALIAS_NUMBER_OF_BELLS])) {
            $entity->setNumberOfBells(
                (int)$result[Repository::ALIAS_NUMBER_OF_BELLS]
            );
        }
        if (isset($result[Repository::ALIAS_COMPOSITION])) {
            $entity->setName(
                $result[Repository::ALIAS_COMPOSITION]
            );
        }
        if (isset($result[Repository::ALIAS_TENOR_TURNED_IN])) {
            $entity->setTenorTurnedIn(
                (bool)$result[Repository::ALIAS_TENOR_TURNED_IN]
            );
        }
        return $entity;
    }

}
