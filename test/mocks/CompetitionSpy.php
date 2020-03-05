<?php
declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\AbstractCompetitionEntity;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use traits\CreateMockDrlCompetitionTrait;

class CompetitionSpy implements CompetitionRepositoryInterface
{
    use CreateMockDrlCompetitionTrait;

    /**
     * @var bool
     */
    private $throwException = false;
    /**
     * @var DrlCompetitionEntity
     */
    private $insertCompetitionValue;
    /**
     * @var bool
     */
    private $insertCompetitionCalled = false;
    /**
     * @var DrlCompetitionEntity
     */
    private $selectCompetitionValue;
    /**
     * @var bool
     */
    private $selectCompetitionCalled = false;

    public function setRepositoryThrowsException(): void
    {
        $this->throwException = true;
    }

    /**
     * @param DrlCompetitionEntity $entity
     * @return DrlCompetitionEntity
     * @throws GeneralRepositoryErrorException
     */
    public function insertCompetition(
        DrlCompetitionEntity $entity
    ): DrlCompetitionEntity {
        $this->insertCompetitionCalled = true;
        if ($this->throwException) {
            throw new GeneralRepositoryErrorException(
                'Unable to add a competition',
                CompetitionRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION
            );
        }
        return $this->insertCompetitionValue ?? $this->createMockDrlCompetition();
    }

    /**
     * @param DrlCompetitionEntity $entity
     */
    public function setInsertCompetitionValue(DrlCompetitionEntity $entity): void
    {
        $this->insertCompetitionValue = $entity;
    }

    /**
     * @return bool
     */
    public function hasInsertCompetitionBeenCalled(): bool
    {
        return $this->insertCompetitionCalled;
    }

    /**
     * @param int $id
     * @return DrlCompetitionEntity
     * @throws RepositoryNoResults
     */
    public function selectCompetition(int $id): DrlCompetitionEntity
    {
        $this->selectCompetitionCalled = true;
        if ($this->throwException) {
            throw new RepositoryNoResults(
                'No competition found',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }
        return $this->selectCompetitionValue ?? $this->createMockDrlCompetition();
    }

    /**
     * @param DrlCompetitionEntity $entity
     */
    public function setSelectCompetitionValue(DrlCompetitionEntity $entity): void
    {
        $this->selectCompetitionValue = $entity;
    }

    /**
     * @return bool
     */
    public function hasSelectCompetitionBeenCalled(): bool
    {
        return $this->selectCompetitionCalled;
    }
}