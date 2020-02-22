<?php
declare(strict_types=1);

namespace core\interactors\competition\createDrlCompetition;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\competition\createDrlCompetition\CreateDrlCompetition;
use DrlArchive\core\interactors\competition\createDrlCompetition\CreateDrlCompetitionRequest;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\DrlCompetitionRepositoryInterface;
use mocks\DrlCompetitionDummy;
use mocks\DrlCompetitionSpy;
use mocks\PreseenterDummy;
use mocks\PresenterSpy;
use mocks\TransactionManagerDummy;
use mocks\TransactionManagerSpy;
use PHPUnit\Framework\TestCase;

class CreateDrlCompetitionTest extends TestCase
{
    public function testInstantiation(): void
    {
        $useCase = $this->createUseCase();

        $this->assertInstanceOf(
            Interactor::class,
            $useCase
        );
    }

    /**
     * @return CreateDrlCompetition
     */
    public function createUseCase(): CreateDrlCompetition
    {
        $request = new CreateDrlCompetitionRequest([
            CreateDrlCompetitionRequest::COMPETITION_NAME => 'Test competition',
            CreateDrlCompetitionRequest::IS_SINGLE_TOWER => true,
        ]);

        $useCase = new CreateDrlCompetition();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PreseenterDummy());
        $useCase->setCompetitionRepository(new DrlCompetitionDummy());
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

    public function testInsertEntity(): void
    {
        $competitionSpy = new DrlCompetitionSpy();
        $useCase = $this->createUseCase();
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $this->assertTrue(
            $competitionSpy->hasInsertCompetitionBeenCalled()
        );
    }

    public function testTransactionIsCommitted(): void
    {
        $transactionSpy = new TransactionManagerSpy();
        $useCase = $this->createUseCase();
        $useCase->setTransactionManager($transactionSpy);
        $useCase->execute();

        $this->assertTrue(
            $transactionSpy->hasCommitTransactionBeenCalled()
        );
    }

    public function testTransactionRollbackOnFailure(): void
    {
        $transactionSpy = new TransactionManagerSpy();
        $competitionSpy = new DrlCompetitionSpy();
        $competitionSpy->setRepositoryThrowsException();

        $useCase = $this->createUseCase();
        $useCase->setTransactionManager($transactionSpy);
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $this->assertTrue(
            $transactionSpy->hasRollbackTransactionBeenCalled()
        );
    }

    public function testSendIsCalled(): void
    {
        $presenterSpy = new PresenterSpy();
        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $this->assertTrue(
            $presenterSpy->hasSendBeenCalled()
        );
    }

    public function testExpectedResponse(): void
    {
        $presenterSpy = new PresenterSpy();
        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );

        $this->assertEquals(
            [
                'id' => 999,
                'name' => 'Test competition',
                'singleTower' => true,
            ],
            $response->getData()
        );
    }

    public function testFailingResponse(): void
    {
        $presenterSpy = new PresenterSpy();
        $competitionSpy = new DrlCompetitionSpy();
        $competitionSpy->setRepositoryThrowsException();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_NOT_CREATED,
            $response->getStatus()
        );

        $this->assertEquals(
            [
                'code' =>
                    DrlCompetitionRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION,
                'message' => 'Unable to add a competition',
            ],
            $response->getData()
        );
    }
}
