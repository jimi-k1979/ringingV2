<?php
declare(strict_types=1);

namespace core\interactors\team\CreateTeam;

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interactors\team\CreateTeam\CreateTeam;
use DrlArchive\core\interactors\team\CreateTeam\CreateTeamRequest;
use DrlArchive\core\interfaces\repositories\DeaneryRepositoryInterface;
use mocks\DeaneryDummy;
use mocks\DeanerySpy;
use mocks\PreseenterDummy;
use mocks\PresenterSpy;
use mocks\TeamDummy;
use mocks\TeamSpy;
use mocks\TransactionManagerDummy;
use mocks\TransactionManagerSpy;
use PHPUnit\Framework\TestCase;
use traits\CreateMockTeamTrait;

class CreateTeamTest extends TestCase
{

    use CreateMockTeamTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            $this->createUseCase()
        );
    }

    private function createUseCase(): CreateTeam
    {
        $request = new CreateTeamRequest(
            [
                CreateTeamRequest::NAME => 'Test Team',
                CreateTeamRequest::DEANERY => 1,
            ]
        );
        $useCase = new CreateTeam();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PreseenterDummy());
        $useCase->setTeamRepository(new TeamDummy());
        $useCase->setDeaneryRepository(new DeaneryDummy());
        $useCase->setTransactionManager(new TransactionManagerDummy());

        return $useCase;
    }

    public function testTransactionHasStarted(): void
    {
        $transactionSpy = new TransactionManagerSpy();
        $useCase = $this->createUseCase();
        $useCase->setTransactionManager($transactionSpy);

        $useCase->execute();

        $this->assertTrue(
            $transactionSpy->hasStartTransactionBeenCalled()
        );

    }

    public function testTransactionRollsBackOnException(): void
    {
        $transactionSpy = new TransactionManagerSpy();
        $deanerySpy = new DeanerySpy();
        $deanerySpy->setRepositoryThrowsException();

        $useCase = $this->createUseCase();
        $useCase->setDeaneryRepository($deanerySpy);
        $useCase->setTransactionManager($transactionSpy);

        $useCase->execute();

        $this->assertTrue(
            $transactionSpy->hasRollbackTransactionBeenCalled()
        );
    }

    public function testNewTeamCreated(): void
    {
        $teamSpy = new TeamSpy();

        $useCase = $this->createUseCase();
        $useCase->setTeamRepository($teamSpy);
        $useCase->execute();

        $this->assertTrue(
            $teamSpy->hasInsertTeamBeenCalled()
        );
    }

    public function testTransactionHasCommitted(): void
    {
        $transactionSpy = new TransactionManagerSpy();

        $useCase = $this->createUseCase();
        $useCase->setTransactionManager($transactionSpy);
        $useCase->execute();

        $this->assertTrue(
            $transactionSpy->hasCommitTransactionBeenCalled()
        );
    }

    public function testSendHasBeenCalled(): void
    {
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $this->assertTrue(
            $presenterSpy->hasSendBeenCalled()
        );
    }

    public function testNewTeamDetails(): void
    {
        $teamSpy = new TeamSpy();
        $presenter = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setTeamRepository($teamSpy);
        $useCase->setPresenter($presenter);
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );

        $this->assertEquals(
            [
                'id' => 123,
                'name' => 'Test team',
                'deanery' => 'Test deanery',
            ],
            $response->getData()
        );
    }

    public function testFailingResponse(): void
    {
        $presenterSpy = new PresenterSpy();
        $deanerySpy = new DeanerySpy();
        $deanerySpy->setRepositoryThrowsException();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setDeaneryRepository($deanerySpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_NOT_CREATED,
            $response->getStatus()
        );
        $this->assertEquals(
            'Team not created',
            $response->getMessage()
        );

        $expectedData = [
            'message' => 'No deanery found',
            'code' => DeaneryRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
        ];
        $this->assertEquals(
            $expectedData,
            $response->getData()
        );
    }
}
