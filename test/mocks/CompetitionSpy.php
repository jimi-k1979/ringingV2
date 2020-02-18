<?php
declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\CompetitionEntity;
use DrlArchive\core\Exceptions\repositories\GeneralRepositoryErrorException;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use traits\CreateMockCompetitionTrait;

class CompetitionSpy implements CompetitionRepositoryInterface
{
    use CreateMockCompetitionTrait;

    /**
     * @var bool
     */
    private $throwException = false;
    /**
     * @var CompetitionEntity
     */
    private $insertCompetitionValue;
    /**
     * @var bool
     */
    private $insertCompetitionCalled = false;
    /**
     * @var CompetitionEntity
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
     * @param CompetitionEntity $entity
     * @return CompetitionEntity
     * @throws GeneralRepositoryErrorException
     */
    public function insertCompetition(
        CompetitionEntity $entity
    ): CompetitionEntity {
        $this->insertCompetitionCalled = true;
        if ($this->throwException) {
            throw new GeneralRepositoryErrorException(
                'Unable to add a competition',
                CompetitionRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION
            );
        }
        return $this->insertCompetitionValue ?? $this->createMockCompetition();
    }

    /**
     * @param CompetitionEntity $entity
     */
    public function setInsertCompetitionValue(CompetitionEntity $entity): void
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
     * @return CompetitionEntity
     * @throws RepositoryNoResults
     */
    public function selectCompetition(int $id): CompetitionEntity
    {
        $this->selectCompetitionCalled = true;
        if ($this->throwException) {
            throw new RepositoryNoResults(
                'No competition found',
                CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }
        return $this->selectCompetitionValue ?? $this->createMockCompetition();
    }

    /**
     * @param CompetitionEntity $entity
     */
    public function setSelectCompetitionValue(CompetitionEntity $entity): void
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