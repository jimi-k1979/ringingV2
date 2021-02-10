<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\AbstractCompetitionEntity;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\OtherCompetitionEntity;

interface CompetitionRepositoryInterface
{
    public const NO_ROWS_CREATED_EXCEPTION = 2401;
    public const NO_ROWS_FOUND_EXCEPTION = 2402;
    public const NO_ROWS_UPDATED_EXCEPTION = 2403;
    public const NO_ROWS_DELETED_EXCEPTION = 2404;

    /**
     * @param DrlCompetitionEntity $entity
     * @return void
     */
    public function insertDrlCompetition(
        DrlCompetitionEntity $entity
    ): void;

    /**
     * @param int $id
     * @return DrlCompetitionEntity
     */
    public function selectDrlCompetition(int $id): DrlCompetitionEntity;

    /**
     * @param string $string
     * @return DrlCompetitionEntity[]
     */
    public function fuzzySearchDrlCompetitions(string $string): array;

    /**
     * @param int $locationId
     * @return DrlCompetitionEntity[]
     */
    public function fetchDrlCompetitionByLocationId(int $locationId): array;

    /**
     * @param string $search
     * @return AbstractCompetitionEntity[]
     */
    public function fuzzySearchAllCompetitions(string $search): array;

    /**
     * @param string $competitionName
     * @return DrlCompetitionEntity
     */
    public function fetchDrlCompetitionByName(
        string $competitionName
    ): DrlCompetitionEntity;

    /**
     * @param string $search
     * @return OtherCompetitionEntity[]
     */
    public function fuzzySearchOtherCompetitions(string $search): array;
}
