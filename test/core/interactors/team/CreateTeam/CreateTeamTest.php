<?php
declare(strict_types=1);

namespace core\interactors\team\CreateTeam;

use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interactors\team\CreateTeam\CreateTeam;
use mocks\DeaneryDummy;
use mocks\PreseenterDummy;
use mocks\TeamDummy;
use mocks\TeamSpy;
use mocks\TransactionManagerDummy;
use PHPUnit\Framework\TestCase;

class CreateTeamTest extends TestCase
{

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            $this->createUseCase()
        );
    }

    private function createUseCase(): CreateTeam
    {
        $useCase = new CreateTeam();
        $useCase->setPresenter(new PreseenterDummy());
        $useCase->setTeamRepository(new TeamDummy());
        $useCase->setDeaneryRepository(new DeaneryDummy());
        $useCase->setTransactionManager(new TransactionManagerDummy());
        return $useCase;
    }

    public function testNewTeamCreated(): void
    {
        $teamRepository = new TeamSpy();
    }
}
