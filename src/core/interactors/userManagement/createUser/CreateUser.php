<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\userManagement\createUser;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\Exceptions\AccessDeniedException;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interactors\Interactor;
use Throwable;

/**
 * Class CreateUser
 * @package DrlArchive\core\interactors\userManagement\createUser
 * @property CreateUserRequest $request
 */
class CreateUser extends Interactor
{

    private UserEntity $createdUser;

    public function execute(): void
    {
        try {
            $this->checkAuthorisationStatus();
            if ($this->request != null) {
                $this->createUserFromRequest();
            }
            $this->createResponse();
        } catch (Throwable $e) {
            $this->createFailureResponse($e);
        }

        $this->sendResponse();
    }

    /**
     * @throws AccessDeniedException
     */
    private function checkAuthorisationStatus(): void
    {
        $this->getUserDetails();
        if ($this->loggedInUser->getId() === null) {
            throw new AccessDeniedException(
                'User not logged in',
                self::NOT_LOGGED_IN_EXCEPTION_CODE
            );
        }
        $this->checkUserIsAuthorised(UserEntity::CONFIRM_DELETE_PERMISSION);
    }

    /**
     * @throws CleanArchitectureException
     */
    private function createUserFromRequest(): void
    {
        $this->createdUser = new UserEntity();
        $this->createdUser->setUsername($this->request->getUsername());
        $this->createdUser->setPassword($this->request->getPassword());
        $this->createdUser->setEmailAddress($this->request->getEmailAddress());

        $this->authenticationManager->adminCreateUser($this->createdUser);
    }

    private function createResponse(): void
    {
        $this->response = new CreateUserResponse();
        $this->response->setStatus(Response::STATUS_SUCCESS);
        if ($this->request != null) {
            $this->response->setData(
                [
                    'username' => $this->createdUser->getUsername(),
                    'emailAddress' => $this->createdUser->getEmailAddress(),
                    'userId' => $this->createdUser->getId(),
                ]
            );
        }
    }

    private function createFailureResponse(Throwable $e)
    {
        $this->response = new CreateUserResponse();
        if ($e->getCode() === self::NOT_LOGGED_IN_EXCEPTION_CODE) {
            $this->response->setStatus(Response::STATUS_UNAUTHORISED);
        } else {
            $this->response->setStatus(Response::STATUS_FORBIDDEN);
        }
        $this->response->setMessage("{$e->getCode()}: {$e->getMessage()}");
    }
}
