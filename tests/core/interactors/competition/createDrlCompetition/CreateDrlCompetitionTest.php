<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\createDrlCompetition;

use DrlArchive\core\classes\Response;
use DrlArchive\core\Exceptions\AccessDeniedException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\AuthenticationManagerSpy;
use DrlArchive\mocks\CompetitionDummy;
use DrlArchive\mocks\CompetitionSpy;
use DrlArchive\mocks\GuestUserDummy;
use DrlArchive\mocks\LoggedInUserDummy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\mocks\SecurityRepositorySpy;
use DrlArchive\mocks\TransactionManagerDummy;
use DrlArchive\mocks\TransactionManagerSpy;
use DrlArchive\TestConstants;
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
        $request = new CreateDrlCompetitionRequest(
            [
                CreateDrlCompetitionRequest::COMPETITION_NAME => TestConstants::TEST_DRL_COMPETITION_NAME,
                CreateDrlCompetitionRequest::IS_SINGLE_TOWER => TestConstants::TEST_DRL_SINGLE_TOWER_COMPETITION,
            ]
        );

        $useCase = new CreateDrlCompetition();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());
        $useCase->setCompetitionRepository(new CompetitionDummy());
        $useCase->setTransactionManager(new TransactionManagerDummy());
        $useCase->setUserRepository(new LoggedInUserDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());

        return $useCase;
    }

    public function testUserIsAuthorised(): void
    {
        $securitySpy = new SecurityRepositorySpy();

        $useCase = $this->createUseCase();
        $useCase->setSecurityRepository($securitySpy);
        $useCase->execute();

        $this->assertTrue(
            $securitySpy->hasIsUserAuthorisedCalled()
        );
    }

    public function testGuestUserIsUnauthorised(): void
    {
        $securitySpy = new SecurityRepositorySpy();
        $authenticationSpy = new AuthenticationManagerSpy();
        $authenticationSpy->setIsLoggedInToFalse();

        $this->expectException(AccessDeniedException::class);

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->setSecurityRepository($securitySpy);
        $useCase->execute();
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
            $competitionSpy->hasInsertDrlCompetitionBeenCalled()
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
                'id' => TestConstants::TEST_DRL_COMPETITION_ID,
                'name' => TestConstants::TEST_DRL_COMPETITION_NAME,
                'singleTower' => TestConstants::TEST_DRL_SINGLE_TOWER_COMPETITION,
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
                    CompetitionRepositoryInterface::NO_ROWS_CREATED_EXCEPTION,
                'message' => 'Unable to add a competition',
            ],
            $response->getData()
        );
    }
}
