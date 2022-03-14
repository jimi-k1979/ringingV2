<?php

namespace DrlArchive\core\interactors\pages\recordsPage;

use DrlArchive\core\classes\Response;
use DrlArchive\core\Exceptions\repositories\RepositoryConnectionErrorException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\AuthenticationManagerSpy;
use DrlArchive\mocks\PageRepositoryDummy;
use DrlArchive\mocks\PageRepositorySpy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\traits\CreateMockStatisticsRecordTrait;
use PHPUnit\Framework\TestCase;

class RecordsPageTest extends TestCase
{
    use CreateMockStatisticsRecordTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new RecordsPage()
        );
    }

    public function testFindOutIfUserIsLoggedIn(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager(
            $authenticationSpy
        );
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasIsLoggedInBeenCalled()
        );
    }

    private function createUseCase(): RecordsPage
    {
        $useCase = new RecordsPage();
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setSecurityRepository(new SecurityRepositoryDummy());
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());
        $useCase->setPageRepository(new PageRepositoryDummy());

        return $useCase;
    }

    public function testUserDetailsFetchedIfLoggedIn(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasLoggedInUserDetailsBeenCalled()
        );
    }

    public function testSendIsCalled(): void
    {
        $presenter = new PresenterSpy();

        $usesCase = $this->createUseCase();
        $usesCase->setPresenter($presenter);
        $usesCase->execute();

        $this->assertTrue(
            $presenter->hasSendBeenCalled()
        );
    }

    public function testListOfStatsFetched(): void
    {
        $pageSpy = new PageRepositorySpy();

        $useCase = $this->createUseCase();
        $useCase->setPageRepository($pageSpy);
        $useCase->execute();

        $this->assertTrue(
            $pageSpy->hasFetchRecordsPageListBeenCalled()
        );
    }

    public function testSuccessfulResponse(): void
    {
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus(),
            'Invalid response status'
        );
        $this->assertEquals(
            [$this->createMockStatisticsRecord()],
            $response->getData(),
            'Invalid response dat'
        );
    }

    public function testBadConnectionFailureResponse(): void
    {
        $presenterSpy = new PresenterSpy();

        $pageRepo = new PageRepositorySpy();
        $pageRepo->setFetchRecordsPageListException(
            new RepositoryConnectionErrorException(
                'Something went wrong',
            )
        );

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setPageRepository($pageRepo);
        $useCase->execute();

        $response = $presenterSpy->getResponse();
        $this->assertEquals(
            Response::STATUS_UNKNOWN_ERROR,
            $response->getStatus(),
            'Invalid response status'
        );
        $this->assertEquals(
            'Unknown error',
            $response->getMessage(),
            'Invalid response message'
        );
    }

}
