<?php
declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\DrlCompetitionEntity;
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
        return new DrlCompetitionEntity();
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
}