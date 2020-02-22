<?php
declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\interfaces\repositories\DrlCompetitionRepositoryInterface;
use traits\CreateMockDrlCompetitionTrait;

class DrlCompetitionDummy implements DrlCompetitionRepositoryInterface
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