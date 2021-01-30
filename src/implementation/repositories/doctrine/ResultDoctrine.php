<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;


use Doctrine\DBAL\Query\QueryBuilder;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\DrlResultEntity;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\Exceptions\repositories\RepositoryConnectionErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryInsertFailedException;
use DrlArchive\core\interfaces\repositories\Repository;
use DrlArchive\core\interfaces\repositories\ResultRepositoryInterface;
use Throwable;

class ResultDoctrine extends DoctrineRepository
    implements ResultRepositoryInterface
{
    private const FIELD_DRL_RESULT_ID = 'dr.id';
    private const FIELD_DRL_POSITION = 'dr.position';
    private const FIELD_DRL_PEAL = 'dr.pealNumber';
    private const FIELD_DRL_FAULTS = 'dr.faults';
    private const FIELD_DRL_TEAM_ID = 'dr.teamsID';
    private const FIELD_TEAM_NAME = 't.teamName';
    private const FIELD_DRL_EVENT_ID = 'dr.eventID';
    private const FIELD_POINTS = 'dr.points';

    /**
     * @inheritDoc
     */
    public function insertDrlResult(DrlResultEntity $result): void
    {
        try {
            $query = $this->database->createQueryBuilder();

            $query->insert('DRL_result')
                ->values(
                    [
                        'position' => ':position',
                        'pealNumber' => ':peal',
                        'faults' => ':faults',
                        'teamID' => ':team',
                        'eventID' => ':event',
                        'points' => ':points',
                    ]
                )
                ->setParameters(
                    [
                        'position' => $result->getPosition(),
                        'peal' => $result->getPealNumber(),
                        'faults' => $result->getFaults(),
                        'team' => $result->getTeam()->getId(),
                        'event' => $result->getEvent()->getId(),
                        'points' => $result->getPoints(),
                    ]
                );
            $rowCount = $query->execute();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No result inserted - connection error',
                ResultRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION
            );
        }

        if ($rowCount === 0) {
            throw new RepositoryInsertFailedException(
                'No result inserted',
                ResultRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION
            );
        }

        $result->setId((int)$this->database->getLastInsertId());
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventResults(DrlEventEntity $event): array
    {
        try {
            $query = $this->baseResultsSelectQuery();
            $query->where(
                $query->expr()->eq(self::FIELD_DRL_EVENT_ID, ':eventId')
            )
                ->setParameter('eventId', $event->getId());
            $results = $query->execute()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No results found - connection error',
                ResultRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        $entitiesArray = $this->generateDrlResultEntityArray($results);
        foreach ($entitiesArray as $entity) {
            $entity->setEvent($event);
        }

        return $entitiesArray;
    }

    private function baseResultsSelectQuery(): QueryBuilder
    {
        $queryBuilder = $this->database->createQueryBuilder();
        $queryBuilder->select(
            [
                self::FIELD_DRL_RESULT_ID . ' AS ' . Repository::ALIAS_RESULT_ID,
                self::FIELD_DRL_POSITION . ' AS ' . Repository::ALIAS_POSITION,
                self::FIELD_DRL_PEAL . ' AS ' . Repository::ALIAS_PEAL_NUMBER,
                self::FIELD_DRL_FAULTS . ' AS ' . Repository::ALIAS_FAULTS,
                self::FIELD_DRL_TEAM_ID . ' AS ' . Repository::ALIAS_TEAM_ID,
                self::FIELD_TEAM_NAME . ' AS ' . Repository::ALIAS_TEAM_NAME,
                self::FIELD_DRL_EVENT_ID . ' AS ' . Repository::ALIAS_EVENT_ID,
                self::FIELD_POINTS . ' AS ' . Repository::ALIAS_POINTS,
            ]
        )
            ->from('DRL_result', 'dr')
            ->innerJoin(
                'dr',
                'team',
                't',
                'dr.teamID = t.id'
            );

        return $queryBuilder;
    }

    private function generateDrlResultEntityArray(array $results): array
    {
        $returnArray = [];
        foreach ($results as $result) {
            $returnArray[] = $this->generateDrlResultEntity($result);
        }

        return $returnArray;
    }

    private function generateDrlResultEntity(array $row): DrlResultEntity
    {
        $entity = new DrlResultEntity();
        $entity->setTeam(new TeamEntity());
        $entity->setEvent(new DrlEventEntity());

        if (isset($row[Repository::ALIAS_RESULT_ID])) {
            $entity->setId((int)$row[Repository::ALIAS_RESULT_ID]);
        }
        if (isset($row[Repository::ALIAS_POSITION])) {
            $entity->setPosition((int)$row[Repository::ALIAS_POSITION]);
        }
        if (isset($row[Repository::ALIAS_PEAL_NUMBER])) {
            $entity->setPealNumber((int)$row[Repository::ALIAS_PEAL_NUMBER]);
        }
        if (isset($row[Repository::ALIAS_FAULTS])) {
            $entity->setFaults((float)$row[Repository::ALIAS_FAULTS]);
        }
        if (isset($row[Repository::ALIAS_POINTS])) {
            $entity->setPosition((int)$row[Repository::ALIAS_POINTS]);
        }
        if (isset($row[Repository::ALIAS_TEAM_ID])) {
            $entity->getTeam()->setId((int)$row[Repository::ALIAS_TEAM_ID]);
        }
        if (isset($row[Repository::ALIAS_TEAM_NAME])) {
            $entity->getTeam()->setName($row[Repository::ALIAS_TEAM_NAME]);
        }
        if (isset($row[Repository::ALIAS_EVENT_ID])) {
            $entity->getEvent()->setId((int)$row[Repository::ALIAS_EVENT_ID]);
        }

        return $entity;
    }
}
