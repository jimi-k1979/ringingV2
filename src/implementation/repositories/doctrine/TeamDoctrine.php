<?php

declare(strict_types=1);

namespace DrlArchive\implementation\repositories\doctrine;


use Doctrine\DBAL\Query\QueryBuilder;
use DrlArchive\core\Constants;
use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\Exceptions\repositories\RepositoryConnectionErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryInsertFailedException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interactors\pages\teamPage\TeamPageRequest;
use DrlArchive\core\interfaces\repositories\Repository;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;
use DrlArchive\core\StatFieldNames;
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
            $rowCount = $query->executeStatement();
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
    public function fetchTeamById(int $teamId): TeamEntity
    {
        try {
            $query = $this->baseTeamSelectQueryBuilder();
            $query->addSelect(
                'MIN(De.year) AS ' . StatFieldNames::FIRST_YEAR,
                'MAX(De.year) AS ' . StatFieldNames::MOST_RECENT_YEAR,
            )
                ->leftJoin(
                    't',
                    'DRL_result',
                    'dr',
                    $query->expr()->eq('t.id', ' dr.teamID')
                )
                ->leftJoin(
                    't',
                    'DRL_event',
                    'de',
                    $query->expr()->eq('dr.eventID', 'de.id')
                )
                ->where(
                    $query->expr()->eq(
                        self::FIELD_TEAM_ID,
                        ':teamId'
                    )
                )
                ->setParameter('teamId', $teamId);
            $result = $query->executeQuery()->fetchAssociative();
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
            $query->executeStatement();
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
            $results = $query->executeQuery()->fetchAllAssociative();
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
     * @param array $row
     * @return TeamEntity
     * @throws CleanArchitectureException
     */
    private function generateTeamEntity(array $row): TeamEntity
    {
        $entity = new TeamEntity();
        $entity->setDeanery(new DeaneryEntity());

        if (isset($row[Repository::ALIAS_TEAM_ID])) {
            $entity->setId((int)$row[Repository::ALIAS_TEAM_ID]);
        }
        if (isset($row[Repository::ALIAS_TEAM_NAME])) {
            $entity->setName($row[Repository::ALIAS_TEAM_NAME]);
        }
        if (isset($row[Repository::ALIAS_DEANERY_ID])) {
            $entity->getDeanery()
                ->setId((int)$row[Repository::ALIAS_DEANERY_ID]);
        }
        if (isset($row[Repository::ALIAS_DEANERY_NAME])) {
            $entity->getDeanery()
                ->setName($row[Repository::ALIAS_DEANERY_NAME]);
        }
        if (isset($row[Repository::ALIAS_DEANERY_REGION])) {
            $entity->getDeanery()
                ->setRegion($row[Repository::ALIAS_DEANERY_REGION]);
        }
        if (
            isset($row[StatFieldNames::FIRST_YEAR])
        ) {
            $entity->setEarliestYear(
                (int)$row[StatFieldNames::FIRST_YEAR]
            );
        }
        if (
            isset($row[StatFieldNames::MOST_RECENT_YEAR])
        ) {
            $entity->setLatestYear(
                (int)$row[StatFieldNames::MOST_RECENT_YEAR]
            );
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
            $result = $query->executeQuery()->fetchAssociative();
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

    /**
     * @param int|null $endYear
     * @param int $startYear
     * @inheritDoc
     */
    public function fetchTeamStatistics(
        TeamEntity $team,
        int $startYear = Constants::MINIMUM_YEAR,
        ?int $endYear = null
    ): array
    {
        [$startYear, $endYear] = $this->yearCheck($startYear, $endYear);

        $returnArray[StatFieldNames::RANGE_SUMMARY] =
            $this->fetchRangeSummaryStats($team, $startYear, $endYear);
        if (isset(
            $returnArray[StatFieldNames::RANGE_SUMMARY][StatFieldNames::FAULT_DIFFERENCE]
        )) {
            $returnArray[StatFieldNames::RANGE_SUMMARY][StatFieldNames::FAULT_DIFFERENCE_TOTAL] =
                $returnArray[StatFieldNames::RANGE_SUMMARY][StatFieldNames::FAULT_DIFFERENCE];
            unset($returnArray[StatFieldNames::RANGE_SUMMARY][StatFieldNames::FAULT_DIFFERENCE]);
        }

        $returnArray[StatFieldNames::SEASONAL] =
            $this->fetchSeasonalStats($team, $startYear, $endYear);

        return $returnArray;
    }

    /**
     * @param int $startYear
     * @param int|null $endYear
     * @return int[]
     */
    public function yearCheck(int $startYear, ?int $endYear): array
    {
        if ($endYear === null) {
            $endYear = (int)date('Y');
        }

        if ($endYear < $startYear) {
            $swap = $startYear;
            $startYear = $endYear;
            $endYear = $swap;
        }

        if ($startYear < Constants::MINIMUM_YEAR) {
            $startYear = Constants::MINIMUM_YEAR;
        }

        return array($startYear, $endYear);
    }

    /**
     * @param TeamEntity $team
     * @param int $startYear
     * @param int $endYear
     * @return array
     * @throws RepositoryConnectionErrorException
     */
    private function fetchRangeSummaryStats(
        TeamEntity $team,
        int $startYear,
        int $endYear
    ): array {
        try {
            $faultDifferenceFieldName = $startYear === $endYear
                ? StatFieldNames::FAULT_DIFFERENCE
                : StatFieldNames::FAULT_DIFFERENCE_TOTAL;

            $query = $this->database->createQueryBuilder();
            $query->select(
                'MIN(De.year) AS ' . StatFieldNames::FIRST_YEAR,
                'MAX(De.year) AS ' . StatFieldNames::MOST_RECENT_YEAR,
                'COUNT(De.id) AS ' . StatFieldNames::EVENT_COUNT,
                'COUNT(DISTINCT De.year) AS ' . StatFieldNames::SEASON_COUNT,
                'COUNT(De.id) / COUNT(DISTINCT De.year) AS ' . StatFieldNames::EVENTS_PER_SEASON,
                'r.meanRanking AS ' . StatFieldNames::RANKING_MEAN,
                'f_team_medianRanking(:teamId, :startYear, :endYear) AS ' . StatFieldNames::RANKING_MEDIAN,
                'r.rankingRange AS ' . StatFieldNames::RANKING_RANGE,
                'AVG(Dr.position) AS ' . StatFieldNames::POSITION_MEAN,
                'f_team_medianPosition(:teamId, :startYear, :endYear) AS ' . StatFieldNames::POSITION_MEDIAN,
                'f_team_modalPosition(:teamId, :startYear, :endYear) AS ' . StatFieldNames::POSITION_MODE,
                'MAX(Dr.position) - MIN(Dr.position) AS ' . StatFieldNames::POSITION_RANGE,
                'SUM(Dr.faults) AS ' . StatFieldNames::FAULT_TOTAL,
                'AVG(Dr.faults) AS ' . StatFieldNames::FAULT_MEAN,
                'f_team_medianFaults(:teamId, :startYear, :endYear) AS ' . StatFieldNames::FAULT_MEDIAN,
                'MAX(Dr.faults) - MIN(Dr.faults) AS ' . StatFieldNames::FAULT_RANGE,
                'f.' . StatFieldNames::FAULT_DIFFERENCE_TOTAL . ' AS ' . $faultDifferenceFieldName,
                'f.' . StatFieldNames::FAULT_DIFFERENCE_MEAN,
                'f_team_medianFaultDifference(:teamId, :startYear, :endYear) AS '
                . StatFieldNames::FAULT_DIFFERENCE_MEDIAN,
                'f.' . StatFieldNames::FAULT_DIFFERENCE_RANGE,
                'SUM(Dr.points) AS ' . StatFieldNames::LEAGUE_POINT_TOTAL,
                'AVG(Dr.points) AS ' . StatFieldNames::LEAGUE_POINT_MEAN,
                'f_team_medianLeaguePoints(:teamId, :startYear, :endYear) AS '
                . StatFieldNames::LEAGUE_POINT_MEDIAN,
                'f_team_modalLeaguePoints(:teamId, :startYear, :endYear) AS '
                . StatFieldNames::LEAGUE_POINT_MODE,
                'MAX(Dr.points) - MIN(Dr.points) AS ' . StatFieldNames::LEAGUE_POINT_RANGE,
                'n.' . StatFieldNames::NO_RESULT_COUNT
            )
                ->from('DRL_result', 'Dr')
                ->from(
                    "({$this->faultDifferenceStatsQuery()->getSQL()})",
                    'f'
                )
                ->from(
                    "({$this->rankingStatsQuery()->getSQL()})",
                    'r'
                )
                ->from(
                    "({$this->noResultCountQuery()->getSQL()})",
                    'n'
                )
                ->innerJoin(
                    'Dr',
                    'DRL_event',
                    'De',
                    $query->expr()->and(
                        $query->expr()->eq('Dr.eventID', 'De.id'),
                        $query->expr()->gte('De.year', ':startYear'),
                        $query->expr()->lte('De.year', ':endYear')
                    )
                )
                ->where(
                    $query->expr()->eq('Dr.teamID', ':teamId')
                )
                ->groupBy(
                    StatFieldNames::FAULT_DIFFERENCE_TOTAL,
                    StatFieldNames::FAULT_DIFFERENCE_RANGE,
                    StatFieldNames::FAULT_DIFFERENCE_MEAN,
                    StatFieldNames::RANKING_MEAN,
                    StatFieldNames::RANKING_RANGE,
                    StatFieldNames::NO_RESULT_COUNT
                )
                ->setParameters(
                    [
                        'teamId' => $team->getId(),
                        'startYear' => $startYear,
                        'endYear' => $endYear,
                    ]
                );
            $results = $query->executeQuery()->fetchAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                "Connection error getting team stats",
                Repository::REPOSITORY_ERROR_CONNECTION,
                $e
            );
        }
        if (empty($results)) {
            return [];
        }
        return $results;
    }

    private function faultDifferenceStatsQuery(): QueryBuilder
    {
        $fdAlias = Repository::ALIAS_FAULT_DIFFERENCE;

        $query = $this->database->createQueryBuilder();
        $query->select(
            "SUM(FDs.{$fdAlias}) AS " . StatFieldNames::FAULT_DIFFERENCE_TOTAL,
            "AVG(FDs.{$fdAlias}) AS " . StatFieldNames::FAULT_DIFFERENCE_MEAN,
            "MAX(FDs.{$fdAlias}) - MIN(FDs.{$fdAlias}) AS " . StatFieldNames::FAULT_DIFFERENCE_RANGE
        )
            ->from("({$this->baseFaultDifferenceCalculatorQuery()->getSQL()}) FDs");

        return $query;
    }

    private function baseFaultDifferenceCalculatorQuery(): QueryBuilder
    {
        $query = $this->database->createQueryBuilder();

        $faultDifferenceField = Repository::ALIAS_FAULT_DIFFERENCE;

        $query->select(
            <<<field
(SUM(r.faults) - D.faults) -
((f_event_numberOfTeams(e.id) - 1) * d.faults)
AS {$faultDifferenceField}
field
        )
            ->from('DRL_event', 'e')
            ->innerJoin(
                'e',
                'DRL_result',
                'r',
                $query->expr()->eq('e.id', 'r.eventID')
            )
            ->innerJoin(
                'e',
                'DRL_result',
                'D',
                $query->expr()->and(
                    $query->expr()->eq('e.id', 'D.eventID'),
                    $query->expr()->eq('D.teamID', ':teamId')
                )
            )
            ->where(
                $query->expr()->gte('e.year', ':startYear'),
                $query->expr()->lte('e.year', ':endYear')
            )
            ->groupBy(
                'D.faults',
                'e.id'
            );

        return $query;
    }

    private function rankingStatsQuery(): QueryBuilder
    {
        $rankAlias = Repository::ALIAS_RANKING;
        $query = $this->database->createQueryBuilder();
        $query->select(
            "AVG({$rankAlias}) AS " . StatFieldNames::RANKING_MEAN,
            "MAX({$rankAlias}) - MIN({$rankAlias}) AS " . StatFieldNames::RANKING_RANGE
        )
            ->from("({$this->baseRankingCalculatorQuery()->getSQL()}) rankings");

        return $query;
    }

    private function baseRankingCalculatorQuery(): QueryBuilder
    {
        $rankingAlias = Repository::ALIAS_RANKING;

        $query = $this->database->createQueryBuilder();
        $query->select(
            <<<field
ROUND(AVG(Dr.points), 2) AS {$rankingAlias}
field
        )
            ->from('DRL_result', 'Dr')
            ->innerJoin(
                'Dr',
                'DRL_event',
                'De',
                $query->expr()->and(
                    $query->expr()->eq('Dr.eventID', 'De.id'),
                    $query->expr()->gte('De.year', ':startYear'),
                    $query->expr()->lte('De.year', ':endYear')
                )
            )
            ->where(
                $query->expr()->eq('Dr.teamID', ':teamId')
            )
            ->groupBy(
                'De.year'
            );

        return $query;
    }

    private function noResultCountQuery(): QueryBuilder
    {
        $query = $this->database->createQueryBuilder();
        $query->select(
            'COUNT(*) AS ' . StatFieldNames::NO_RESULT_COUNT
        )
            ->from('DRL_result', 'r')
            ->innerJoin(
                'r',
                'DRL_event',
                'e',
                $query->expr()->and(
                    $query->expr()->eq('r.eventID', 'e.id'),
                    $query->expr()->gte('e.year', ':startYear'),
                    $query->expr()->lte('e.year', ':endYear')
                )
            )
            ->where(
                $query->expr()->eq('r.teamID', ':teamId'),
                $query->expr()->eq('r.faults', 0),
                $query->expr()->eq('r.points', 0)
            );

        return $query;
    }

    /**
     * @param TeamEntity $team
     * @param int $startYear
     * @param int $endYear
     * @return array
     * @throws RepositoryConnectionErrorException
     */
    private function fetchSeasonalStats(
        TeamEntity $team,
        int $startYear,
        int $endYear
    ): array {
        $returnArray = [];
        for ($year = $startYear; $year <= $endYear; $year++) {
            $stats = $this->fetchRangeSummaryStats($team, $year, $year);
            if (!empty($stats)) {
                $returnArray[(string)$year] = $stats;
            }
        }

        return $returnArray;
    }

    /**
     * @inheritDoc
     */
    public function fetchTeamResults(
        TeamEntity $team,
        int $startYear = Constants::MINIMUM_YEAR,
        ?int $endYear = null
    ): array {
        [$startYear, $endYear] = $this->yearCheck($startYear, $endYear);

        try {
            $query = $this->database->createQueryBuilder();
            $query->select(
                'De.id AS ' . Repository::ALIAS_EVENT_ID,
                'l.id AS ' . Repository::ALIAS_LOCATION_ID,
                'Dc.id AS ' . Repository::ALIAS_COMPETITION_ID,
                'Dc.isSingleTower AS ' . Repository::ALIAS_IS_SINGLE_TOWER,
                'De.isUnusualTower AS ' . Repository::ALIAS_IS_UNUSUAL_TOWER,
                'Dc.competitionName AS ' . Repository::ALIAS_COMPETITION_NAME,
                'l.location AS ' . Repository::ALIAS_LOCATION_NAME,
                'De.year AS ' . Repository::ALIAS_YEAR,
                'Dr.position AS ' . Repository::ALIAS_POSITION,
                'f_event_numberOfTeams(De.id) AS ' . Repository::ALIAS_ENTRIES,
                'Dr.faults AS ' . Repository::ALIAS_FAULTS,
                'Dr.points AS ' . Repository::ALIAS_POINTS,
                "({$this->resultsListFaultDifferenceQuery()->getSQL()}) AS "
                . Repository::ALIAS_FAULT_DIFFERENCE
            )
                ->from('DRL_result', 'Dr')
                ->innerJoin(
                    'Dr',
                    'DRL_event',
                    'De',
                    $query->expr()->and(
                        $query->expr()->eq('Dr.eventId', 'De.Id'),
                        $query->expr()->gte('De.year', ':startYear'),
                        $query->expr()->lte('De.year', ':endYear')
                    )
                )
                ->innerJoin(
                    'Dr',
                    'DRL_competition',
                    'Dc',
                    $query->expr()->eq('De.competitionID', 'Dc.id')
                )
                ->innerJoin(
                    'Dr',
                    'location',
                    'l',
                    $query->expr()->eq('De.locationID', 'l.id')
                )
                ->where(
                    $query->expr()->eq('Dr.teamID', ':teamId')
                )
                ->orderBy('De.year', 'ASC')
                ->addOrderBy('Dc.competitionName', 'ASC')
                ->setParameters(
                    [
                        'teamId' => $team->getId(),
                        'startYear' => $startYear,
                        'endYear' => $endYear,
                    ]
                );
            return $query->executeQuery()->fetchAllAssociative();
        } catch (Throwable $e) {
            throw new RepositoryConnectionErrorException(
                'Connection error fetching team results list',
                Repository::REPOSITORY_ERROR_CONNECTION,
                $e
            );
        }
    }

    private function resultsListFaultDifferenceQuery(): QueryBuilder
    {
        $query = $this->database->createQueryBuilder();
        $query->select(
            '(SUM(r.faults) - D.faults) - ((f_event_numberOfTeams(Dr.eventID) - 1) * d.faults)'
        )
            ->from('DRL_event', 'e')
            ->innerJoin(
                'e',
                'DRL_result',
                'r',
                $query->expr()->eq('e.id', 'r.eventID')
            )
            ->innerJoin(
                'e',
                'DRL_result',
                'D',
                $query->expr()->and(
                    $query->expr()->eq('e.id', 'D.eventID'),
                    $query->expr()->eq('D.teamID', 'Dr.teamID')
                )
            )
            ->where(
                $query->expr()->eq('e.id', 'Dr.eventID')
            )
            ->groupBy(
                'D.faults'
            );
        return $query;
    }

}
