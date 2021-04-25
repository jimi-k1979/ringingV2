<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\TestConstants;
use DrlArchive\traits\CreateMockDrlCompetitionTrait;

class CompetitionDummy implements CompetitionRepositoryInterface
{
    use CreateMockDrlCompetitionTrait;

    public function insertDrlCompetition(
        DrlCompetitionEntity $entity
    ): void {
        $entity->setId(TestConstants::TEST_DRL_COMPETITION_ID);
    }

    public function selectDrlCompetition(int $id): DrlCompetitionEntity
    {
        return $this->createMockDrlCompetition();
    }

    /**
     * @inheritDoc
     */
    public function fuzzySearchDrlCompetitions(string $string): array
    {
        return [$this->createMockDrlCompetition()];
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlCompetitionByUsualLocationId(int $locationId): array
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

    /**
     * @inheritDoc
     */
    public function fuzzySearchOtherCompetitions(string $search): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fetchDrlCompetitionByLocation(LocationEntity $location): array
    {
        return [];
    }
}
