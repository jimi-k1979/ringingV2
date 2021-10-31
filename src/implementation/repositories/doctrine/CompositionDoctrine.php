<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;

use Doctrine\DBAL\Query\QueryBuilder;
use DrlArchive\core\entities\ChangeEntity;
use DrlArchive\core\entities\CompositionEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\RepositoryConnectionErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
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

    /**
     * @inheritDoc
     */
    public function fetchCompositionById(int $id): CompositionEntity
    {
        try {
            $query = $this->baseFetchAllCompositionFieldsSelect();
            $query->where(
                $query->expr()->eq('c.id', ':id')
            )
                ->setParameter('id', $id);
            $result = $query->executeQuery()->fetchAssociative();
        } catch (\Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No composition found - database connection error',
                Repository::REPOSITORY_ERROR_CONNECTION
            );
        }

        if (empty($result)) {
            throw new RepositoryNoResultsException(
                'No composition found',
                CompositionRepositoryInterface::NO_RESULTS_FOUND_EXCEPTION_CODE
            );
        }

        return $this->generateCompositionEntity($result);
    }

    /**
     * @inheritDoc
     */
    public function fetchChangesByComposition(CompositionEntity $composition): void
    {
        try {
            $query = $this->database->createQueryBuilder();

            $query->select(
                'chg.changeNumber AS ' . Repository::ALIAS_CHANGE_NUMBER,
                'chg.upBell AS ' . Repository::ALIAS_UP_BELL,
                'chg.downBell AS ' . Repository::ALIAS_DOWN_BELL,
                'chg.bellToFollow AS ' . Repository::ALIAS_BELL_TO_FOLLOW,
            )
                ->from('`change`', 'chg')
                ->where(
                    $query->expr()->eq('chg.compositionID', ':id')
                )
                ->orderBy(Repository::ALIAS_CHANGE_NUMBER)
                ->setParameter('id', $composition->getId());
            $results = $query->executeQuery()->fetchAllAssociative();
        } catch (\Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No changes found - database connection error',
                Repository::REPOSITORY_ERROR_CONNECTION
            );
        }

        $composition->setChanges(
            $this->generateChangeEntityArray($results)
        );
    }

    /**
     * @param array $results
     * @return ChangeEntity[]
     */
    private function generateChangeEntityArray(array $results): array
    {
        $returnArray = [];
        foreach ($results as $result) {
            $returnArray[] = $this->generateChangeEntity($result);
        }
        return $returnArray;
    }

    private function generateChangeEntity($result): ChangeEntity
    {
        $entity = new ChangeEntity();

        if (isset($result[Repository::ALIAS_CHANGE_ID])) {
            $entity->setId(
                (int)$result[Repository::ALIAS_CHANGE_ID]
            );
        }
        if (isset($result[Repository::ALIAS_CHANGE_NUMBER])) {
            $entity->setChangeNumber(
                (int)$result[Repository::ALIAS_CHANGE_NUMBER]
            );
        }
        if (isset($result[Repository::ALIAS_UP_BELL])) {
            $entity->setUpBell(
                (int)$result[Repository::ALIAS_UP_BELL]
            );
        }
        if (isset($result[Repository::ALIAS_DOWN_BELL])) {
            $entity->setDownBell(
                (int)$result[Repository::ALIAS_DOWN_BELL]
            );
        }
        if (isset($result[Repository::ALIAS_BELL_TO_FOLLOW])) {
            $entity->setBellToFollow(
                (int)$result[Repository::ALIAS_BELL_TO_FOLLOW]
            );
        }
        return $entity;
    }
}
