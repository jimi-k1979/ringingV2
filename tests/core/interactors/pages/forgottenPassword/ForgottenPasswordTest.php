<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\forgottenPassword;

use DrlArchive\core\classes\Response;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\AuthenticationManagerSpy;
use DrlArchive\mocks\EmailClientDummy;
use DrlArchive\mocks\EmailClientSpy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\TestConstants;
use PHPUnit\Framework\TestCase;

class ForgottenPasswordTest extends TestCase
{
    public function testRequestDefaults(): void
    {
        $request = new ForgottenPasswordRequest();

        $this->assertNull(
            $request->getEmailAddress()
        );
        $this->assertNull(
            $request->getSelector()
        );
        $this->assertNull(
            $request->getToken()
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

    private function createUseCase(): ForgottenPassword
    {
        $useCase = new ForgottenPassword();
        $useCase->setRequest(new ForgottenPasswordRequest());
        $useCase->setPresenter(new PresenterDummy());
        $useCase->setAuthenticationManager(new AuthenticationManagerDummy());
        $useCase->setEmailClient(new EmailClientDummy());

        return $useCase;
    }

    public function testFirstPageLoadResponse(): void
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
                ForgottenPassword::DATA_TEMPLATE => 'getEmail',
            ],
            $response->getData()
        );
        $this->assertEquals(
            '',
            $response->getMessage()
        );
    }

    public function testRequestPasswordResetIsCalled(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createRequestResetUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasRequestPasswordResetBeenCalled()
        );
    }

    private function createRequestResetUseCase(): ForgottenPassword
    {
        $request = new ForgottenPasswordRequest();
        $request->setEmailAddress(TestConstants::TEST_USER_EMAIL);

        $useCase = $this->createUseCase();
        $useCase->setRequest($request);
        return $useCase;
    }

    public function testPasswordRequestSendsEmail(): void
    {
        $emailSpy = new EmailClientSpy();

        $useCase = $this->createRequestResetUseCase();
        $useCase->setEmailClient($emailSpy);
        $useCase->execute();

        $this->assertTrue(
            $emailSpy->hasSendMessageBeenCalled()
        );
    }

    public function testPasswordRequestSuccessfulResponse(): void
    {
        $presenter = new PresenterSpy();

        $useCase = $this->createRequestResetUseCase();
        $useCase->setPresenter($presenter);
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );
        $this->assertEquals(
            [
                ForgottenPassword::DATA_TEMPLATE => 'getEmail',
            ],
            $response->getData()
        );
        $this->assertEquals(
            ForgottenPassword::EMAIL_SENT_SUCCESSFULLY,
            $response->getMessage()
        );
    }

    public function testTokenAndSelectorVerified(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createVerificationUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasVerifyResetAttemptBeenCalled()
        );
    }

    private function createVerificationUseCase(): ForgottenPassword
    {
        $request = new ForgottenPasswordRequest();
        $request->setSelector('testSelector');
        $request->setToken('testToken');

        $useCase = $this->createUseCase();
        $useCase->setRequest($request);

        return $useCase;
    }

    public function testSuccessfulVerificationResponse(): void
    {
        $presenter = new PresenterSpy();

        $useCase = $this->createVerificationUseCase();
        $useCase->setPresenter($presenter);
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );
        $this->assertEquals(
            [
                ForgottenPassword::DATA_TEMPLATE =>
                    ForgottenPassword::TEMPLATE_GET_NEW_PASSWORD,
                ForgottenPassword::DATA_TOKEN => 'testToken',
                ForgottenPassword::DATA_SELECTOR => 'testSelector',
            ],
            $response->getData()
        );
        $this->assertEquals(
            '',
            $response->getMessage()
        );
    }

    public function testFailureVerificationResponseNoSelector(): void
    {
        $presenter = new PresenterSpy();

        $request = new ForgottenPasswordRequest();
        $request->setToken('testToken');

        $useCase = $this->createUseCase();
        $useCase->setRequest($request);
        $useCase->setPresenter($presenter);
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_UNAUTHORISED,
            $response->getStatus()
        );
        $this->assertEquals(
            [
                ForgottenPassword::DATA_TEMPLATE =>
                    ForgottenPassword::TEMPLATE_GET_EMAIL,
            ],
            $response->getData()
        );
        $this->assertEquals(
            'Incorrect data provided, have you got the correct link?',
            $response->getMessage()
        );
    }

    public function testFailureVerificationResponseBadData(): void
    {
        $presenter = new PresenterSpy();

        $authenticationSpy = new AuthenticationManagerSpy();
        $authenticationSpy->setVerifyResetAttemptThrowsException();

        $useCase = $this->createVerificationUseCase();
        $useCase->setPresenter($presenter);
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_UNAUTHORISED,
            $response->getStatus()
        );
        $this->assertEquals(
            [
                ForgottenPassword::DATA_TEMPLATE =>
                    ForgottenPassword::TEMPLATE_GET_EMAIL,
            ],
            $response->getData()
        );
        $this->assertEquals(
            'Incorrect data provided, have you got the correct link?',
            $response->getMessage()
        );
    }

    public function testChangePasswordIsCalled(): void
    {
        $authenticationSpy = new AuthenticationManagerSpy();

        $useCase = $this->createChangePasswordUseCase();
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $this->assertTrue(
            $authenticationSpy->hasCompleteResetAttemptBeenCalled()
        );
    }

    private function createChangePasswordUseCase(): ForgottenPassword
    {
        $request = new ForgottenPasswordRequest();
        $request->setToken('testToken');
        $request->setSelector('testSelector');
        $request->setNewPassword('newPassword');

        $useCase = $this->createUseCase();
        $useCase->setRequest($request);
        return $useCase;
    }

    public function testSuccessfulResetResponse(): void
    {
        $presenter = new PresenterSpy();

        $useCase = $this->createChangePasswordUseCase();
        $useCase->setPresenter($presenter);
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );
        $this->assertEquals(
            [
                ForgottenPassword::DATA_TEMPLATE =>
                    ForgottenPassword::TEMPLATE_GET_EMAIL,
            ],
            $response->getData()
        );
        $this->assertEquals(
            ForgottenPassword::PASSWORD_RESET_SUCCESSFULLY,
            $response->getMessage()
        );
    }

    public function testFailingResetResponse(): void
    {
        $presenter = new PresenterSpy();

        $authenticationSpy = new AuthenticationManagerSpy();
        $authenticationSpy->setCompleteResetAttemptThrowsException();

        $useCase = $this->createChangePasswordUseCase();
        $useCase->setPresenter($presenter);
        $useCase->setAuthenticationManager($authenticationSpy);
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_BAD_REQUEST,
            $response->getStatus()
        );
        $this->assertEquals(
            [
                ForgottenPassword::DATA_TEMPLATE =>
                    ForgottenPassword::TEMPLATE_GET_NEW_PASSWORD,
                ForgottenPassword::DATA_TOKEN => 'testToken',
                ForgottenPassword::DATA_SELECTOR => 'testSelector',
            ],
            $response->getData()
        );
        $this->assertEquals(
            'Unable to change password, please try again - 1309: Something went wrong',
            $response->getMessage()
        );
    }


}
