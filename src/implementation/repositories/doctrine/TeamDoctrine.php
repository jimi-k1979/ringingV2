<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;


use Doctrine\DBAL\Query\QueryBuilder;
use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\RepositoryConnectionErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryInsertFailedException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\Repository;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;
use Throwable;

class TeamDoctrine extends DoctrineRepository implements
    TeamRepositoryInterface
{
    private const FIELD_TEAM_ID = 't.id';
    private const FIELD_TEAM_NAME = 't.teamName';
    private const FIELD_DEANERY_ID = 't.deaneryID';
    private const FIELD_DEANERY_NAME = 'd.deaneryName';
    private const FIELD_DEANERY_REGION = 'd.northSouth';

    /**
     * @inheritDoc
     */
    public function insertTeam(TeamEntity $teamEntity): void
    {
        try {
            $query = $this->database->createQueryBuilder();

            $query->insert('team')
                ->values(
                    [
                        'teamName' => ':name',
                        'deaneryID' => ':deaneryId',
                    ]
                )
                ->setParameters(
                    [
                        'name' => $teamEntity->getName(),
                        'deaneryId' => $teamEntity->getDeanery()->getId()
                    ]
                );
            $rowCount = $query->execute();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'Team insert failed - connection error',
                TeamRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION
            );
        }

        if ($rowCount === 0) {
            throw new RepositoryInsertFailedException(
                'Team insert failed',
                TeamRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION
            );
        }

        $teamEntity->setId((int)$this->database->getLastInsertId());
    }

    /**
     * @inheritDoc
     */
    public function selectTeam(int $teamId): TeamEntity
    {
        try {
            $query = $this->baseTeamSelectQueryBuilder();
            $query->where(
                $query->expr()->eq(
                    self::FIELD_TEAM_ID,
                    ':teamId'
                )
            )
                ->setParameter('teamId', $teamId);
            $result = $query->execute()->fetchAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No team found - connection error',
                TeamRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        if (empty($result)) {
            throw new RepositoryNoResultsException(
                'No team found',
                TeamRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateTeamEntity($result);
    }

    /**
     * @inheritDoc
     */
    public function updateTeam(TeamEntity $teamEntity): void
    {
        try {
            $query = $this->database->createQueryBuilder();

            $query->update('team', 't')
                ->set(self::FIELD_TEAM_NAME, ':name')
                ->set(self::FIELD_DEANERY_ID, ':deaneryId')
                ->where(
                    $query->expr()->eq(self::FIELD_TEAM_ID, ':id')
                )
                ->setParameters(
                    [
                        'name' => $teamEntity->getName(),
                        'deaneryId' => $teamEntity->getDeanery()->getId()
                    ]
                );
            $query->execute();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'Team not updated - connection error',
                TeamRepositoryInterface::NO_ROWS_UPDATED
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function fuzzySearchTeam(string $searchTerm): array
    {
        try {
            $query = $this->baseTeamSelectQueryBuilder();

            $query->where(
                $query->expr()->like(
                    self::FIELD_TEAM_NAME,
                    ':search'
                )
            )
                ->setParameter('search', "%{$searchTerm}%");
            $results = $query->execute()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'Unable to fetch teams - connection error',
                TeamRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateTeamEntityArray($results);
    }

    private function baseTeamSelectQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->database->createQueryBuilder();
        $queryBuilder->select(
            [
                self::FIELD_TEAM_ID . ' AS ' . Repository::ALIAS_TEAM_ID,
                self::FIELD_TEAM_NAME . ' AS ' . Repository::ALIAS_TEAM_NAME,
                self::FIELD_DEANERY_ID . ' AS ' . Repository::ALIAS_DEANERY_ID,
                self::FIELD_DEANERY_NAME . ' AS ' . Repository::ALIAS_DEANERY_NAME,
                self::FIELD_DEANERY_REGION . ' AS ' . Repository::ALIAS_DEANERY_REGION,
            ]
        )
            ->from('team', 't')
            ->innerJoin(
                't',
                'deanery',
                'd',
                't.deaneryID = d.id'
            );

        return $queryBuilder;
    }

    /**
     * @param array $result
     * @return TeamEntity
     * @throws CleanArchitectureException
     */
    private function generateTeamEntity(array $result): TeamEntity
    {
        $entity = new TeamEntity();
        $entity->setDeanery(new DeaneryEntity());

        if (isset($result[Repository::ALIAS_TEAM_ID])) {
            $entity->setId((int)$result[Repository::ALIAS_TEAM_ID]);
        }
        if (isset($result[Repository::ALIAS_TEAM_NAME])) {
            $entity->setName($result[Repository::ALIAS_TEAM_NAME]);
        }
        if (isset($result[Repository::ALIAS_DEANERY_ID])) {
            $entity->getDeanery()
                ->setId((int)$result[Repository::ALIAS_DEANERY_ID]);
        }
        if (isset($result[Repository::ALIAS_DEANERY_NAME])) {
            $entity->getDeanery()
                ->setName($result[Repository::ALIAS_DEANERY_NAME]);
        }
        if (isset($result[Repository::ALIAS_DEANERY_REGION])) {
            $entity->getDeanery()
                ->setRegion($result[Repository::ALIAS_DEANERY_REGION]);
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamByName(string $teamName): TeamEntity
    {
        try {
            $query = $this->baseTeamSelectQueryBuilder();
            $query->where(
                $query->expr()->eq(
                    self::FIELD_TEAM_NAME,
                    ':name'
                )
            )
                ->setParameter('name', $teamName);
            $result = $query->execute()->fetchAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No team found - connection error',
                TeamRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        if (empty($result)) {
            throw new RepositoryNoResultsException(
                'No team found',
                TeamRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateTeamEntity($result);
    }

    /**
     * @param array $results
     * @return TeamEntity[]
     * @throws CleanArchitectureException
     */
    private function generateTeamEntityArray(array $results): array
    {
        $returnArray = [];
        foreach ($results as $result) {
            $returnArray[] = $this->generateTeamEntity($result);
        }

        return $returnArray;
    }
}
