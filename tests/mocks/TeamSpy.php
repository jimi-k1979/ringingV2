<?php

declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResults;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;
use traits\CreateMockTeamTrait;

class TeamSpy implements TeamRepositoryInterface
{
    use CreateMockTeamTrait;

    /**
     * @var TeamEntity
     */
    private $insertTeamValue;
    /**
     * @var TeamEntity
     */
    private $selectTeamValue;
    /**
     * @var TeamEntity
     */
    private $updateTeamValue;
    /**
     * @var bool
     */
    private $deleteTeamValue = false;
    /**
     * @var bool
     */
    private $insertCalled = false;
    /**
     * @var bool
     */
    private $selectCalled = false;
    /**
     * @var bool
     */
    private $updateCalled = false;
    /**
     * @var bool
     */
    private $deletedCalled = false;
    /**
     * @var TeamEntity[]
     */
    private $fuzzySearchValue;
    /**
     * @var bool
     */
    private $fuzzySearchCalled = false;
    /**
     * @var bool
     */
    private $fuzzySearchThrowsException = false;

    /**
     * @param TeamEntity $teamEntity
     * @return TeamEntity
     */
    public function insertTeam(TeamEntity $teamEntity): TeamEntity
    {
        $this->insertCalled = true;

        return $this->insertTeamValue ?? $this->createMockTeam();
    }

    /**
     * @param TeamEntity $teamEntity
     */
    public function setInsertTeamValue(TeamEntity $teamEntity): void
    {
        $this->insertTeamValue = $teamEntity;
    }

    /**
     * @return bool
     */
    public function hasInsertTeamBeenCalled(): bool
    {
        return $this->insertCalled;
    }

    /**
     * @param int $teamId
     * @return TeamEntity
     */
    public function selectTeam(int $teamId): TeamEntity
    {
        $this->selectCalled = true;

        return $this->selectTeamValue ?? $this->createMockTeam();
    }

    /**
     * @param TeamEntity $teamEntity
     */
    public function setSelectTeamValue(TeamEntity $teamEntity): void
    {
        $this->selectTeamValue = $teamEntity;
    }

    /**
     * @return bool
     */
    public function hasSelectTeamBeenCalled(): bool
    {
        return $this->selectCalled;
    }

    /**
     * @param TeamEntity $teamEntity
     * @return TeamEntity
     */
    public function updateTeam(TeamEntity $teamEntity): TeamEntity
    {
        $this->updateCalled = true;

        return $this->updateTeamValue ?? $this->createMockTeam();
    }

    /**
     * @param TeamEntity $teamEntity
     */
    public function setUpdateTeamValue(TeamEntity $teamEntity): void
    {
        $this->updateTeamValue = $teamEntity;
    }

    /**
     * @return bool
     */
    public function hasUpdateTeamBeenCalled(): bool
    {
        return $this->updateCalled = true;
    }

    /**
     * @param TeamEntity $teamEntity
     * @return bool
     */
    public function deleteTeam(TeamEntity $teamEntity): bool
    {
        $this->deletedCalled = true;

        return $this->deleteTeamValue;
    }

    public function setDeleteTeamValue(): void
    {
        $this->deleteTeamValue = true;
    }

    /**
     * @return bool
     */
    public function hasDeleteTeamBeenCalled(): bool
    {
        return $this->deletedCalled;
    }

    /**
     * @inheritDoc
     * @throws RepositoryNoResults
     */
    public function fuzzySearchTeam(string $searchTerm): array
    {
        $this->fuzzySearchCalled = true;

        if ($this->fuzzySearchThrowsException) {
            throw new RepositoryNoResults(
                'No teams found',
                TeamRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fuzzySearchValue ?? [$this->createMockTeam()];
    }

    public function setFuzzySearchValue(array $results): void
    {
        $this->fuzzySearchValue = $results;
    }

    public function hasFuzzySearchTeamBeenCalled(): bool
    {
        return $this->fuzzySearchCalled;
    }

    public function setFuzzySearchThrowsException(): void
    {
        $this->fuzzySearchThrowsException = true;
    }
}