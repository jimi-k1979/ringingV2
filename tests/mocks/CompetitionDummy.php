<?php

declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\AbstractCompetitionEntity;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use traits\CreateMockDrlCompetitionTrait;

class CompetitionDummy implements CompetitionRepositoryInterface
{
    use CreateMockDrlCompetitionTrait;

    public function insertDrlCompetition(
        DrlCompetitionEntity $entity
    ): DrlCompetitionEntity {
        return $this->createMockDrlCompetition();
    }

    public function selectDrlCompetition(int $id): DrlCompetitionEntity
    {
        return $this->createMockDrlCompetition();
    }

    /**
     * @inheritDoc
     */
    public function fuzzySearchDrlCompetition(string $string): array
    {
        return [$this->createMockDrlCompetition()];
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlCompetitionByLocationId(int $locationId): array
    {
        return [$this->createMockDrlCompetition()];
    }

    /**
     * @inheritDoc
     */
    public function fuzzySearchAllCompetitions(string $search): array
    {
        return [$this->createMockDrlCompetition()];
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlCompetitionByName(
        string $competitionName
    ): DrlCompetitionEntity {
        return $this->createMockDrlCompetition();
    }
}