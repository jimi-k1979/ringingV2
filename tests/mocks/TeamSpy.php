<?php

declare(strict_types=1);

namespace DrlArchive\mocks;


use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\Exceptions\repositories\RepositoryNoResultsException;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;
use DrlArchive\TestConstants;
use DrlArchive\traits\CreateMockTeamTrait;

class TeamSpy implements TeamRepositoryInterface
{
    use CreateMockTeamTrait;

    private int $insertTeamIdValue = TestConstants::TEST_TEAM_ID;
    private bool $insertCalled = false;
    private TeamEntity $selectTeamValue;
    private bool $selectCalled = false;
    private TeamEntity $updateTeamValue;
    private bool $updateCalled = false;
    private bool $deleteTeamValue = false;
    private bool $deletedCalled = false;
    /**
     * @var TeamEntity[]
     */
    private array $fuzzySearchValue;
    private bool $fuzzySearchCalled = false;
    private bool $fuzzySearchThrowsException = false;
    private bool $fetchTeamByNameCalled = false;
    private int $fetchTeamByNameCallCount = 0;
    private bool $fetchTeamByNameThrowsException = false;
    private TeamEntity $fetchTeamByNameValue;

    /**
     * @param TeamEntity $teamEntity
     * @return void
     */
    public function insertTeam(TeamEntity $teamEntity): void
    {
        $this->insertCalled = true;

        $teamEntity->setId($this->insertTeamIdValue);
    }

    /**
     * @param int $id
     */
    public function setInsertTeamIdValue(int $id): void
    {
        $this->insertTeamIdValue = $id;
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
     * @return void
     */
    public function updateTeam(TeamEntity $teamEntity): void
    {
        $this->updateCalled = true;

        if (isset($this->updateTeamValue)) {
            $teamEntity = $this->updateTeamValue;
        }
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
     * @throws RepositoryNoResultsException
     */
    public function fuzzySearchTeam(string $searchTerm): array
    {
        $this->fuzzySearchCalled = true;

        if ($this->fuzzySearchThrowsException) {
            throw new RepositoryNoResultsException(
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

    /**
     * @inheritDoc
     */
    public function fetchTeamByName(string $teamName): TeamEntity
    {
        $this->fetchTeamByNameCalled = true;
        $this->fetchTeamByNameCallCount++;
        if ($this->fetchTeamByNameThrowsException) {
            throw new RepositoryNoResultsException(
                'Team not found',
                TeamRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
            );
        }

        return $this->fetchTeamByNameValue ?? $this->createMockTeam();
    }

    public function hasFetchTeamByNameBeenCalled(): bool
    {
        return $this->fetchTeamByNameCalled;
    }

    public function getFetchTeamByNameCallCount(): int
    {
        return $this->fetchTeamByNameCallCount;
    }

    public function setFetchTeamByNameThrowsException(): void
    {
        $this->fetchTeamByNameThrowsException = true;
    }

    public function setFetchTeamByNameValue(TeamEntity $value): void
    {
        $this->fetchTeamByNameValue = $value;
    }

}
