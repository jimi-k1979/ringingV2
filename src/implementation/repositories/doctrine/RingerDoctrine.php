<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;


use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\entities\RingerEntity;
use DrlArchive\core\entities\WinningRingerEntity;
use DrlArchive\core\Exceptions\repositories\RepositoryConnectionErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\Repository;
use DrlArchive\core\interfaces\repositories\RingerRepositoryInterface;

class RingerDoctrine extends DoctrineRepository implements
    RingerRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function fuzzySearchRinger(string $searchTerm): array
    {
        // TODO: Implement fuzzySearchRinger() method.
        return [];
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function fetchWinningTeamByEvent(DrlEventEntity $event): array
    {
        $query = $this->createAllFieldsBaseQuery();

        $query->addSelect(
            'dewr.bell AS ' . self::ALIAS_BELL
        )
            ->innerJoin(
                'r',
                'DRL_event_winning_ringer',
                'dewr',
                'r.id = dewr.ringerID'
            )
            ->where(
                $query->expr()->eq('dewr.eventID', ':eventId')
            )
            ->orderBy(self::ALIAS_BELL)
            ->setParameter('eventId', $event->getId());

        $results = $query->executeQuery()->fetchAllAssociative();

        return $this->generateWinningTeam($results);
    }

    private function generateWinningTeam(array $results): array
    {
        $team = [];
        foreach ($results as $result) {
            $winningRinger = new WinningRingerEntity();
            if (isset($result[Repository::ALIAS_BELL])) {
                $winningRinger->setBell(
                    $result[Repository::ALIAS_BELL]
                );
            }
            $winningRinger->setRinger(
                $this->generateRingerEntity($result)
            );
            $team[] = $winningRinger;
        }

        return $team;
    }

    /**
     * @inheritDoc
     */
    public function fetchRingerById(int $ringerId): RingerEntity
    {
        try {
            $query = $this->createAllFieldsBaseQuery();
            $query->addSelect(
                    'j.id AS ' . Repository::ALIAS_JUDGE_ID
                )
                ->leftJoin(
                    'r',
                    'judge',
                    'j',
                    'r.id = j.ringerId'
                )
                ->where(
                    $query->expr()->eq(
                        'r.id',
                        ':ringerId'
                    )
                )
                ->setParameter('ringerId', $ringerId);
            $result = $query->executeQuery()->fetchAssociative();
        } catch (\Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'Cannot fetch ringer - connection error',
                RingerRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        if (empty($result)) {
            throw new RepositoryNoResultsException(
                'Ringer not found',
                RingerRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->generateRingerEntity($result);
    }

    private function createAllFieldsBaseQuery(): QueryBuilder
    {
        $queryBuilder = $this->database->createQueryBuilder();

        $queryBuilder->select(
            'r.id AS ' . Repository::ALIAS_RINGER_ID,
            'r.firstName AS ' . Repository::ALIAS_FIRST_NAME,
            'r.lastName AS ' . Repository::ALIAS_LAST_NAME,
            'r.notes AS ' . Repository::ALIAS_NOTES
        )
            ->from('ringer', 'r');

        return $queryBuilder;
    }

    private function generateRingerEntity(array $result): RingerEntity
    {
        $entity = new RingerEntity();

        if (isset($result[Repository::ALIAS_RINGER_ID])) {
            $entity->setId(
                (int)$result[Repository::ALIAS_RINGER_ID]
            );
        }
        if (isset($result[Repository::ALIAS_FIRST_NAME])) {
            $entity->setFirstName(
                $result[Repository::ALIAS_FIRST_NAME]
            );
        }
        if (isset($result[Repository::ALIAS_LAST_NAME])) {
            $entity->setLastName(
                $result[Repository::ALIAS_LAST_NAME]
            );
        }
        if (isset($result[Repository::ALIAS_NOTES])) {
            $entity->setNotes(
                $result[Repository::ALIAS_NOTES]
            );
        }
        if (isset($result[Repository::ALIAS_JUDGE_ID])) {
            $entity->setJudgeId(
                (int)$result[Repository::ALIAS_JUDGE_ID]
            );
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function fetchWinningRingerDetailsByRinger(RingerEntity $ringer): array
    {
        $query = $this->database->createQueryBuilder();
        $query->select(
            'de.id AS ' . Repository::ALIAS_EVENT_ID,
            'dewr.bell AS ' . Repository::ALIAS_BELL,
            'de.year AS ' . Repository::ALIAS_YEAR,
            'dc.isSingleTower AS ' . Repository::ALIAS_IS_SINGLE_TOWER,
            'dc.numberOfBells AS ' . Repository::ALIAS_NUMBER_OF_BELLS,
            'de.isUnusualTower AS ' . Repository::ALIAS_IS_UNUSUAL_TOWER,
            'dc.competitionName AS ' . Repository::ALIAS_COMPETITION_NAME,
            'IF(el.location IS NULL, cl.location, el.location) AS ' . Repository::ALIAS_LOCATION_NAME
        )
            ->from('DRL_event_winning_ringer', 'dewr')
            ->innerJoin(
                'dewr',
                'DRL_event',
                'de',
                'dewr.eventID = de.id'
            )
            ->innerJoin(
                'dewr',
                'DRL_competition',
                'dc',
                'de.competitionID = dc.id'
            )
            ->leftJoin(
                'dewr',
                'location',
                'el',
                'de.locationID = el.id'
            )
            ->leftJoin(
                'dewr',
                'location',
                'cl',
                'dc.usualLocationID = cl.id'
            )
            ->where(
                $query->expr()->eq('dewr.ringerID', ':ringerId')
            )
            ->orderBy(
                Repository::ALIAS_YEAR
            )
            ->setParameter(
                'ringerId',
                $ringer->getId()
            );

        $results = $query->executeQuery()->fetchAllAssociative();

        $eventList = [];
        foreach ($results as $result) {
            $competition = new DrlCompetitionEntity();
            $competition->setName($result[Repository::ALIAS_COMPETITION_NAME]);
            $competition->setSingleTowerCompetition((bool)$result[Repository::ALIAS_IS_SINGLE_TOWER]);
            $competition->setNumberOfBells($result[Repository::ALIAS_NUMBER_OF_BELLS]);

            $location = new LocationEntity();
            $location->setLocation($result[Repository::ALIAS_LOCATION_NAME]);

            $event = new DrlEventEntity();
            $event->setId((int)$result[Repository::ALIAS_EVENT_ID]);
            $event->setYear($result[Repository::ALIAS_YEAR]);
            $event->setUnusualTower((bool)$result[Repository::ALIAS_IS_UNUSUAL_TOWER]);
            $event->setLocation($location);
            $event->setCompetition($competition);

            $winningRinger = new WinningRingerEntity();
            $winningRinger->setBell($result[Repository::ALIAS_BELL]);
            $winningRinger->setEvent($event);
            $winningRinger->setRinger($ringer);

            $eventList[] = $winningRinger;
        }
        return $eventList;
    }
}
