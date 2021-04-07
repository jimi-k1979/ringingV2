<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\userManagement\loginUser;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\Exceptions\AccessDeniedException;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interactors\Interactor;

use Throwable;

use function Webmozart\Assert\Tests\StaticAnalysis\null;

/**
 * Class LoginUser
 * @package DrlArchive\core\interactors\userManagement\loginUser
 * @property LoginUserRequest $request
 */
class LoginUser extends Interactor
{
    public const REDIRECT_TO = 'redirectTo';
    public const EMAIL_ADDRESS = 'emailAddress';

    /**
     * @var ?UserEntity
     */
    private ?UserEntity $user = null;

    public function execute(): void
    {
        try {
            $this->processRequest();
            $this->createResponse();
        } catch (Throwable $e) {
            $this->createFailureResponse($e);
        }
        $this->sendResponse();
    }

    /**
     * @throws CleanArchitectureException
     */
    private function processRequest(): void
    {
        if (!empty($this->request->getPassword())) {
            $this->createUserEntity();

            $this->authenticationManager->loginUser(
                $this->user
            );
            $this->user = $this->authenticationManager->loggedInUserDetails();
        }
    }

    private function createUserEntity(): void
    {
        $this->user = new UserEntity();
        $this->user->setEmailAddress(
            $this->request->getEmailAddress()
        );
        $this->user->setPassword(
            $this->request->getPassword()
        );
    }

    private function createResponse(): void
    {
        $this->response = new LoginUserResponse();
        $this->response->setStatus(Response::STATUS_SUCCESS);

        $responseData = [
            self::REDIRECT_TO => $this->request->getRedirectTo(),
        ];

        $this->response->setData($responseData);
        if (
            $this->user !== null
            && !empty($this->user->getId())
        ) {
            $this->response->setLoggedInUser($this->user);
        }
    }

    private function createFailureResponse(Throwable $e): void
    {
        $this->response = new LoginUserResponse();
        if ($e instanceof AccessDeniedException) {
            $this->response->setStatus(Response::STATUS_FORBIDDEN);
        } else {
            $this->response->setStatus(Response::STATUS_UNKNOWN_ERROR);
        }
        $this->response->setData(
            [
                self::REDIRECT_TO => $this->request->getRedirectTo(),
                self::EMAIL_ADDRESS => $this->request->getEmailAddress(),
            ]
        );
        $this->response->setMessage(
            'Invalid username or password'
        );
    }
}
