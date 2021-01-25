<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;


use Doctrine\DBAL\Query\QueryBuilder;
use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\RepositoryConnectionErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
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

    public function insertTeam(TeamEntity $teamEntity): void
    {
        // TODO: Implement insertTeam() method.
    }

    /**
     * @param int $teamId
     * @return TeamEntity
     * @throws CleanArchitectureException
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
            throw new RepositoryNoResults(
                'No team found',
                TeamRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateTeamEntity($result);
    }

    public function updateTeam(TeamEntity $teamEntity): TeamEntity
    {
        // TODO: Implement updateTeam() method.
    }

    public function deleteTeam(TeamEntity $teamEntity): bool
    {
        // TODO: Implement deleteTeam() method.
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

        $returnArray = [];
        foreach ($results as $result) {
            $returnArray[] = $this->generateTeamEntity($result);
        }

        return $returnArray;
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
                ->setParameter('team', $teamName);
            $result = $query->execute()->fetchAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No team found - connection error',
                TeamRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        if (empty($result)) {
            throw new RepositoryNoResults(
                'No team found',
                TeamRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateTeamEntity($result);
    }
}
