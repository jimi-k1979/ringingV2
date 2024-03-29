<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;


use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\JudgeEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\RepositoryConnectionErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryInsertFailedException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\EventRepositoryInterface;
use DrlArchive\core\interfaces\repositories\Repository;
use Throwable;

class EventDoctrine extends DoctrineRepository implements
    EventRepositoryInterface
{
    private const FIELD_DRL_EVENT_ID = 'de.id';
    private const FIELD_DRL_YEAR = 'de.year';
    private const FIELD_DRL_IS_UNUSUAL_TOWER = 'de.isUnusualTower';
    private const FIELD_DRL_COMPETITION_ID = 'de.competitionID';
    private const FIELD_DRL_LOCATION_ID = 'de.locationID';
    private const FIELD_DRL_COMPETITION_NAME = 'dc.competitionName';
    private const FIELD_LOCATION_NAME = 'l.location';
    private const FIELD_DRL_USUAL_LOCATION_ID = 'dc.usualLocationID';
    private const FIELD_USUAL_LOCATION_NAME = 'ul.location';
    private const FIELD_DRL_SINGLE_TOWER = 'dc.isSingleTower';
    private const FIELD_DRL_IS_SINGLE_TOWER = 'dc.isSingleTower';

    /**
     * @inheritDoc
     */
    public function insertDrlEvent(DrlEventEntity $entity): void
    {
        try {
            $query = $this->database->createQueryBuilder();
            $query->insert('DRL_event')
                ->values(
                    [
                        'year' => ':year',
                        'competitionID' => ':comp',
                        'locationID' => ':location',
                        'isUnusualTower' => ':isUnusualTower',
                    ]
                )
                ->setParameters(
                    [
                        'year' => $entity->getYear(),
                        'comp' => $entity->getCompetition()->getId(),
                        'location' => $entity->getLocation()->getId(),
                        'isUnusualTower' => (int)$entity->isUnusualTower(),
                    ]
                );
            $rows = $query->executeStatement();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No event inserted - connection error',
                EventRepositoryInterface::NO_ROWS_CREATED_EXCEPTION
            );
        }

        if ($rows === 0) {
            throw new RepositoryInsertFailedException(
                'No event inserted',
                EventRepositoryInterface::NO_ROWS_CREATED_EXCEPTION
            );
        }

        $entity->setId((int)$this->database->getLastInsertId());
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEvent(int $id): DrlEventEntity
    {
        try {
            $query = $this->baseDrlEventSelectQuery();
            $query->where(self::FIELD_DRL_EVENT_ID . ' = :id')
                ->setParameter('id', $id);
            $result = $query->executeQuery()->fetchAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No event found - connection error',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        if (empty($result)) {
            throw new RepositoryNoResultsException(
                'No event found',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateDrlEventEntity($result);
    }

    /**
     * @return QueryBuilder
     */
    private function baseDrlEventSelectQuery(): QueryBuilder
    {
        $queryBuilder = $this->database->createQueryBuilder();
        $queryBuilder->select(
            [
                self::FIELD_DRL_EVENT_ID . ' AS ' . Repository::ALIAS_EVENT_ID,
                self::FIELD_DRL_YEAR . ' AS ' . Repository::ALIAS_YEAR,
                self::FIELD_DRL_IS_UNUSUAL_TOWER . ' AS ' . Repository::ALIAS_IS_UNUSUAL_TOWER,
                self::FIELD_DRL_COMPETITION_ID . ' AS ' . Repository::ALIAS_COMPETITION_ID,
                self::FIELD_DRL_LOCATION_ID . ' AS ' . Repository::ALIAS_LOCATION_ID,
                self::FIELD_DRL_COMPETITION_NAME . ' AS ' . Repository::ALIAS_COMPETITION_NAME,
                self::FIELD_DRL_IS_SINGLE_TOWER . ' AS ' . Repository::ALIAS_IS_SINGLE_TOWER,
                self::FIELD_LOCATION_NAME . ' AS ' . Repository::ALIAS_LOCATION_NAME,
            ]
        )
            ->from('DRL_event', 'de')
            ->leftJoin(
                'de',
                'DRL_competition',
                'dc',
                'de.competitionID = dc.id'
            )
            ->leftJoin(
                'de',
                'location',
                'l',
                'de.locationID = l.id'
            );

        return $queryBuilder;
    }

    /**
     * @param array $row
     * @return DrlEventEntity
     */
    private function generateDrlEventEntity(array $row): DrlEventEntity
    {
        $entity = new DrlEventEntity();
        $entity->setCompetition(new DrlCompetitionEntity());
        $entity->setLocation(new LocationEntity());
        $entity->getCompetition()->setUsualLocation(new LocationEntity());

        if (isset($row[Repository::ALIAS_EVENT_ID])) {
            $entity->setId((int)$row[Repository::ALIAS_EVENT_ID]);
        }
        if (isset($row[Repository::ALIAS_YEAR])) {
            $entity->setYear($row[Repository::ALIAS_YEAR]);
        }
        if (isset($row[Repository::ALIAS_IS_UNUSUAL_TOWER])) {
            $entity->setUnusualTower(
                (bool)$row[Repository::ALIAS_IS_UNUSUAL_TOWER]
            );
        }
        if (isset($row[Repository::ALIAS_COMPETITION_ID])) {
            $entity->getCompetition()
                ->setId((int)$row[Repository::ALIAS_COMPETITION_ID]);
        }
        if (isset($row[Repository::ALIAS_LOCATION_ID])) {
            $entity->getLocation()
                ->setId((int)$row[Repository::ALIAS_LOCATION_ID]);
        }
        if (isset($row[Repository::ALIAS_COMPETITION_NAME])) {
            $entity->getCompetition()
                ->setName($row[Repository::ALIAS_COMPETITION_NAME]);
        }
        if (isset($row[Repository::ALIAS_LOCATION_NAME])) {
            $entity->getLocation()
                ->setLocation($row[Repository::ALIAS_LOCATION_NAME]);
        }
        if (isset($row[Repository::ALIAS_USUAL_LOCATION_ID])) {
            $entity->getCompetition()
                ->getUsualLocation()
                ->setId((int)$row[Repository::ALIAS_USUAL_LOCATION_ID]);
        }
        if (isset($row[Repository::ALIAS_USUAL_LOCATION_NAME])) {
            $entity->getCompetition()
                ->getUsualLocation()
                ->setLocation($row[Repository::ALIAS_USUAL_LOCATION_NAME]);
        }
        if (isset($row[Repository::ALIAS_IS_SINGLE_TOWER])) {
            $entity->getCompetition()
                ->setSingleTowerCompetition(
                    (bool)$row[Repository::ALIAS_IS_SINGLE_TOWER]
                );
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventsByCompetitionId(int $competitionId): array
    {
        try {
            $query = $this->baseDrlEventSelectQuery();
            $query->where(self::FIELD_DRL_COMPETITION_ID . ' = :competition')
                ->orderBy(Repository::ALIAS_YEAR)
                ->setParameter('competition', $competitionId);

            $results = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No event found - connection error',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateDrlEventEntityArray($results);
    }

    /**
     * @param array $results
     * @return DrlEventEntity[]
     */
    private function generateDrlEventEntityArray(array $results): array
    {
        $returnArray = [];
        foreach ($results as $result) {
            $returnArray[] = $this->generateDrlEventEntity($result);
        }

        return $returnArray;
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventsByCompetitionAndLocationIds(
        int $competitionId,
        int $locationId
    ): array {
        try {
            $query = $this->baseDrlEventSelectQuery();
            $query->where(
                $query->expr()->and(
                    $query->expr()->eq(self::FIELD_DRL_COMPETITION_ID, ':comp'),
                    $query->expr()->eq(self::FIELD_DRL_LOCATION_ID, ':location')
                )
            )
                ->setParameters(
                    [
                        'comp' => $competitionId,
                        'location' => $locationId
                    ]
                );
            $results = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No event found - connection error',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateDrlEventEntityArray($results);
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventsByYear(string $year): array
    {
        try {
            $query = $this->baseDrlEventSelectQuery();
            $query->where(self::FIELD_DRL_YEAR . ' = :year')
                ->orderBy(Repository::ALIAS_COMPETITION_NAME)
                ->setParameter('year', $year);

            $results = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No event found - connection error',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateDrlEventEntityArray($results);
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventByYearAndCompetitionName(
        string $year,
        string $competitionName
    ): DrlEventEntity {
        try {
            $query = $this->baseDrlEventSelectQuery();
            $query->addSelect(
                [
                    self::FIELD_DRL_USUAL_LOCATION_ID . ' AS ' . Repository::ALIAS_USUAL_LOCATION_ID,
                    self::FIELD_USUAL_LOCATION_NAME . ' AS ' . Repository::ALIAS_USUAL_LOCATION_NAME,
                    self::FIELD_DRL_SINGLE_TOWER . ' AS ' . Repository::ALIAS_IS_SINGLE_TOWER,
                ]
            )
                ->leftJoin(
                    'de',
                    'location',
                    'ul',
                    'dc.usualLocationID = ul.id'
                )
                ->where(
                    $query->expr()->and(
                        $query->expr()->eq(
                            self::FIELD_DRL_COMPETITION_NAME,
                            ':compName'
                        ),
                        $query->expr()->eq(self::FIELD_DRL_YEAR, ':year')
                    )
                )
                ->setParameters(
                    [
                        'compName' => $competitionName,
                        'year' => $year
                    ]
                );

            $result = $query->executeQuery()->fetchAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No event found - connection error',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        if (empty($result)) {
            throw new RepositoryNoResultsException(
                'No event found',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateDrlEventEntity($result);
    }

    public function fetchDrlEventByYearAndCompetitionId(
        string $year,
        int $competitionId
    ): DrlEventEntity {
        try {
            $query = $this->baseDrlEventSelectQuery();
            $query->where(
                $query->expr()->and(
                    $query->expr()->eq(
                        self::FIELD_DRL_YEAR,
                        ':year'
                    ),
                    $query->expr()->eq(
                        self::FIELD_DRL_COMPETITION_ID,
                        ':competitionId'
                    )
                )
            )
                ->setParameters(
                    [
                        'year' => $query,
                        'competitionId' => $competitionId
                    ]
                );
            $result = $query->executeQuery()->fetchAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No event found - connection error',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        if (empty($result)) {
            throw new RepositoryNoResultsException(
                'No event found',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateDrlEventEntity($result);
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventsByCompetitionName(string $name): array
    {
        try {
            $query = $this->baseDrlEventSelectQuery();
            $query->where(self::FIELD_DRL_COMPETITION_NAME . ' = :competition')
                ->orderBy(Repository::ALIAS_YEAR)
                ->setParameter('competition', $name);

            $results = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No event found - connection error',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateDrlEventEntityArray($results);
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventsByCompetitionIdAndVenue(
        int $competitionId,
        string $locationName
    ): array {
        try {
            $query = $this->baseDrlEventSelectQuery();
            $query->where(
                $query->expr()->and(
                    $query->expr()->eq(self::FIELD_DRL_COMPETITION_ID, ':comp'),
                    $query->expr()->eq(self::FIELD_LOCATION_NAME, ':location')
                )
            )
                ->orderBy(Repository::ALIAS_YEAR)
                ->setParameters(
                    [
                        'comp' => $competitionId,
                        'location' => $locationName
                    ]
                );
            $results = $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'No event found - connection error',
                EventRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateDrlEventEntityArray($results);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function fetchSingleDrlEventStatistics(DrlEventEntity $event): void
    {
        $query = $this->database->createQueryBuilder();
        $query->select(
            'SUM(dr.faults) AS ' . Repository::ALIAS_TOTAL_FAULTS,
            'AVG(dr.faults) AS ' . Repository::ALIAS_MEAN_FAULTS,
            'margin.margin AS ' . Repository::ALIAS_WINNING_MARGIN
        )
            ->from('DRL_result', 'dr')
            ->innerJoin(
                'dr',
                "({$this->eventMarginQuery()})",
                'margin',
                'dr.eventID = margin.eventID'
            )
            ->where(
                $query->expr()->and(
                    $query->expr()->neq('dr.faults', 0),
                    $query->expr()->eq('dr.eventID', ':eventId')
                )
            )
            ->groupBy('margin.margin')
            ->setParameter('eventId', $event->getId());
        $result = $query->executeQuery()->fetchAssociative();

        if (empty($result)) {
            throw new RepositoryNoResultsException(
                'No statistics for that event'
            );
        }

        $event->setTotalFaults((float)$result[Repository::ALIAS_TOTAL_FAULTS]);
        $event->setMeanFaults((float)$result[Repository::ALIAS_MEAN_FAULTS]);
        $event->setWinningMargin((float)$result[Repository::ALIAS_WINNING_MARGIN]);
    }

    private function eventMarginQuery(): string
    {
        $query = $this->database->createQueryBuilder();
        $query->select(
            'dr2.faults - dr1.faults AS margin',
            'dr1.eventID'
        )
            ->from('DRL_result', 'dr1')
            ->innerJoin(
                'dr1',
                'DRL_result',
                'dr2',
                'dr1.eventID = dr2.eventID AND dr2.position = 2'
            )
            ->where(
                $query->expr()->and(
                    $query->expr()->eq('dr1.position', 1),
                    $query->expr()->eq('dr1.eventID', ':eventId')
                )
            );

        return $query->getSQL();
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlEventListByJudge(JudgeEntity $judge): array
    {
        $query = $this->database->createQueryBuilder();

        $query->select(
            'de.id AS ' . Repository::ALIAS_EVENT_ID,
            'de.year AS ' . Repository::ALIAS_YEAR,
            'de.isUnusualTower AS ' . Repository::ALIAS_IS_UNUSUAL_TOWER,
            'dc.isSingleTower AS ' . Repository::ALIAS_IS_SINGLE_TOWER,
            'dc.competitionName AS ' . Repository::ALIAS_COMPETITION_NAME,
            'l.location AS ' . Repository::ALIAS_LOCATION_NAME,
        )
            ->from('DRL_event', 'de')
            ->innerJoin(
                'de',
                'DRL_event_judge',
                'dej',
                $query->expr()->and(
                    $query->expr()->eq(
                        'de.id',
                        'dej.eventID'
                    ),
                    $query->expr()->eq(
                        'dej.judgeID',
                        ':judgeId'
                    )
                )
            )
            ->innerJoin(
                'de',
                'DRL_competition',
                'dc',
                $query->expr()->eq(
                    'de.competitionID',
                    'dc.id'
                )
            )
            ->innerJoin(
                'de',
                'location',
                'l',
                $query->expr()->eq(
                    'de.locationID',
                    'l.id'
                )
            )
            ->setParameter('judgeId', $judge->getId());
        $results = $query->executeQuery()->fetchAllAssociative();

        return $this->generateDrlEventEntityArray($results);
    }

}
