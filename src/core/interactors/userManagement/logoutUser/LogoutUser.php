<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\userManagement\logoutUser;


use DrlArchive\core\classes\Response;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interactors\Interactor;

/**
 * Class LogoutUser
 * @package DrlArchive\core\interactors\userManagement\logoutUser
 * @property LogoutUserRequest $request
 */
class LogoutUser extends Interactor
{

    public const DATA_FIELD_REDIRECT_TO = 'redirectTo';

    public function execute(): void
    {
        try {
            $this->logoutUser();
        } catch (\Throwable $e) {
            // swallow exception and redirect anyway
        }
        $this->createResponse();
        $this->sendResponse();
    }

    /**
     * @throws CleanArchitectureException
     */
    private function logoutUser(): void
    {
        $this->authenticationManager->logOutUser();
    }

    private function createResponse(): void
    {
        $this->response = new LogoutUserResponse();
        $this->response->setStatus(Response::STATUS_SUCCESS);
        $this->response->setData(
            [
                self::DATA_FIELD_REDIRECT_TO => $this->request->getRedirectTo(),
            ]
        );
    }

}
