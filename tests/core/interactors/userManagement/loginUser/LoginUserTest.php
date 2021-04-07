<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\userManagement\loginUser;

use DrlArchive\core\classes\Response;
use DrlArchive\core\Constants;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\AuthenticationManagerSpy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\TestConstants;
use DrlArchive\traits\CreateMockUserTrait;
use PHPUnit\Framework\TestCase;

class LoginUserTest extends TestCase
{
    use CreateMockUserTrait;

    public function testRequestDefaults(): void
    {
        $request = new LoginUserRequest();

        $this->assertNull(
            $request->getEmailAddress()
        );
        $this->assertNull(
            $request->getUsername()
        );
        $this->assertNull(
            $request->getPassword()
        );
        $this->assertEquals(
            Constants::INDEX_PAGE_ADDRESS,
            $request->getRedirectTo()
        );
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new LoginUser()
        );
    }

    public function testSendIsCalled(): void
    {
        $presenter = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenter);
        $useCase->execute();

        $this->assertTrue(
            $presenter->hasSendBeenCalled()
        );
    }

    private function createUseCase(): LoginUser
    {
        $useCase = new LoginUser();
        $useCase->setRequest(new LoginUserRequest());
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());
        $useCase->setPresenter(new PresenterDummy());

        return $useCase;
    }

    public function testEmptyRequestSuccess(): void
    {
        $presenter = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenter);
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );
        $this->assertEquals(
            [
                LoginUser::REDIRECT_TO => '/index.php',
            ],
            $response->getData()
        );
        $this->assertNull(
            $response->getLoggedInUser()
        );
    }

    public function testLoginUserIsCalled(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createLoggingInUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasLoginUserBeenCalled()
        );
    }

    private function createLoggingInUseCase(): LoginUser
    {
        $request = new LoginUserRequest();
        $request->setEmailAddress(TestConstants::TEST_USER_EMAIL);
        $request->setPassword(TestConstants::TEST_USER_PASSWORD);
        $request->setRedirectTo(TestConstants::TEST_REDIRECT_TO);

        $useCase = $this->createUseCase();
        $useCase->setRequest($request);

        return $useCase;
    }

    public function testEmptyRequestDoesNotAuthenticate(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertFalse(
            $authenticationSpy->hasLoginUserBeenCalled()
        );
    }

    public function testFailureToLoginResponse(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();
        $authenticationSpy->setLoginUserThrowsException();

        $presenterSpy = new PresenterSpy();

        $useCase = $this->createLoggingInUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_FORBIDDEN,
            $response->getStatus()
        );
        $this->assertEquals(
            [
                LoginUser::EMAIL_ADDRESS => TestConstants::TEST_USER_EMAIL,
                LoginUser::REDIRECT_TO => TestConstants::TEST_REDIRECT_TO,
            ],
            $response->getData()
        );
        $this->assertEquals(
            'Invalid username or password',
            $response->getMessage()
        );
        $this->assertNull(
            $response->getLoggedInUser()
        );
    }

    public function testLoggedInDetailsFetched(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();
        $authenticationSpy->setLoggedInUserDetailsValue(
            $this->createMockSuperAdmin()
        );

        $useCase = $this->createLoggingInUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasLoggedInUserDetailsBeenCalled()
        );
    }


    public function testSuccessfulLoginResponse(): void
    {
        $presenterSpy = new PresenterSpy();

        $authenticationSpy = new AuthenticationManagerSpy();
        $authenticationSpy->setLoggedInUserDetailsValue(
            $this->createMockSuperAdmin()
        );

        $useCase = $this->createLoggingInUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );
        $this->assertEquals(
            [
                LoginUser::REDIRECT_TO => TestConstants::TEST_REDIRECT_TO,
            ],
            $response->getData()
        );
        $this->assertEquals(
            $this->createMockSuperAdmin(),
            $response->getLoggedInUser()
        );
    }

}
