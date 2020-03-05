<?php
declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use traits\CreateMockDrlCompetitionTrait;

class CompetitionDummy implements CompetitionRepositoryInterface
{
    use CreateMockDrlCompetitionTrait;

    public function insertCompetition(
        DrlCompetitionEntity $entity
    ): DrlCompetitionEntity {
        return $this->createMockDrlCompetition();
    }

    public function selectCompetition(int $id): DrlCompetitionEntity
    {
        return new DrlCompetitionEntity();
    }
}