<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\userManagement\createUser;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\AuthenticationManagerSpy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\mocks\SecurityRepositorySpy;
use DrlArchive\TestConstants;
use DrlArchive\traits\CreateMockUserTrait;
use PHPUnit\Framework\TestCase;

class CreateUserTest extends TestCase
{
    use CreateMockUserTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new CreateUser()
        );
    }

    public function testUserDetailsFetched(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasLoggedInUserDetailsBeenCalled()
        );
    }

    private function createUseCase(): CreateUser
    {
        $useCase = new CreateUser();
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());
        $useCase->setPresenter(new PresenterDummy());
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

    public function testCreateUserIsCalled(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();
        $authenticationSpy->setLoggedInUserDetailsValue(
            $this->createMockSuperAdmin()
        );

        $useCase = $this->createUseCaseWithRequest();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasAdminCreateUserBeenCalled()
        );
    }

    private function createUseCaseWithRequest(): CreateUser
    {
        $request = new CreateUserRequest();
        $request->setUsername(TestConstants::TEST_USER_USERNAME);
        $request->setEmailAddress(TestConstants::TEST_USER_EMAIL);
        $request->setPassword(TestConstants::TEST_USER_PASSWORD);

        $useCase = $this->createUseCase();
        $useCase->setRequest($request);

        return $useCase;
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

    public function testSuccessfulResponseMadeUser(): void
    {
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCaseWithRequest();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );
        $this->assertEquals(
            [
                'username' => TestConstants::TEST_USER_USERNAME,
                'emailAddress' => TestConstants::TEST_USER_EMAIL,
                'userId' => TestConstants::TEST_USER_ID
            ],
            $response->getData()
        );
    }

    public function testSuccessfulResponseNoRequest(): void
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
        $this->assertEmpty(
            $response->getData()
        );
    }

    public function testFailureResponseNotLoggedIn(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();
        $authenticationSpy->setIsLoggedInToFalse();

        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_UNAUTHORISED,
            $response->getStatus()
        );
        $this->assertEquals(
            '9902: User not logged in',
            $response->getMessage()
        );
    }

    public function testFailureResponseNoPermissions(): void
    {
        $securitySpy = new SecurityRepositorySpy();
        $securitySpy->setIsAuthorisedResponseToFalse();

        $presenter = new PresenterSpy();
        $useCase = $this->createUseCase();
        $useCase->setSecurityRepository($securitySpy);
        $useCase->setPresenter($presenter);
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_FORBIDDEN,
            $response->getStatus()
        );
        $this->assertEquals(
            '9901: Not authorised to view this page',
            $response->getMessage()
        );
    }


}
