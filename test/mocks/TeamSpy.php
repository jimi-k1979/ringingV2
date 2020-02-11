<?php
declare(strict_types=1);

namespace mocks;


use DrlArchive\core\entities\TeamEntity;
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
     * @return TeamEntity
     */
    public function insertTeam(): TeamEntity
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
     * @return TeamEntity
     */
    public function selectTeam(): TeamEntity
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
     * @return TeamEntity
     */
    public function updateTeam(): TeamEntity
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
     * @return bool
     */
    public function deleteTeam(): bool
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
}