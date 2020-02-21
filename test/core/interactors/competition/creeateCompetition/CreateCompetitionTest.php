<?php
declare(strict_types=1);

namespace core\interactors\competition\createCompetition;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\competition\createCompetition\CreateCompetition;
use DrlArchive\core\interactors\competition\createCompetition\CreateCompetitionRequest;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use mocks\CompetitionDummy;
use mocks\CompetitionSpy;
use mocks\PreseenterDummy;
use mocks\PresenterSpy;
use mocks\TransactionManagerDummy;
use mocks\TransactionManagerSpy;
use PHPUnit\Framework\TestCase;

class CreateCompetitionTest extends TestCase
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
     * @return CreateCompetition
     */
    public function createUseCase(): CreateCompetition
    {
        $request = new CreateCompetitionRequest([
            CreateCompetitionRequest::COMPETITION_NAME => 'Test competition',
            CreateCompetitionRequest::IS_SINGLE_TOWER => true,
        ]);

        $useCase = new CreateCompetition();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PreseenterDummy());
        $useCase->setCompetitionRepository(new CompetitionDummy());
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
        $competitionSpy = new CompetitionSpy();
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
        $competitionSpy = new CompetitionSpy();
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
        $competitionSpy = new CompetitionSpy();
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
                    CompetitionRepositoryInterface::UNABLE_TO_INSERT_EXCEPTION,
                'message' => 'Unable to add a competition',
            ],
            $response->getData()
        );
    }
}
