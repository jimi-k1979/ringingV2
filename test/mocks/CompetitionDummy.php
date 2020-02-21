<?php
declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\CompetitionEntity;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use traits\CreateMockCompetitionTrait;

class CompetitionDummy implements CompetitionRepositoryInterface
{
    use CreateMockCompetitionTrait;

    public function insertCompetition(
        CompetitionEntity $entity
    ): CompetitionEntity {
        return $this->createMockCompetition();
    }

    public function selectCompetition(int $id): CompetitionEntity
    {
        return new CompetitionEntity();
    }
}