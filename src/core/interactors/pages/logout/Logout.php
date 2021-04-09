<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\logout;


use DrlArchive\core\classes\Response;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\managers\AuthenticationManagerInterface;
use Throwable;

/**
 * Class Logout
 * @package DrlArchive\core\interactors\pages\logout
 * @property LogoutRequest $request
 */
class Logout extends Interactor
{

    public function execute(): void
    {
        try {
            $this->logoutUser();
            $this->createResponse();
        } catch (Throwable $e) {
            $this->createFailureResponse($e);
        }
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
        $this->response = new LogoutResponse();
        $this->response->setStatus(Response::STATUS_LOGGED_OUT);
        $this->response->setData(
            [
                'forwardTo' => $this->request->getForwardTo()
            ]
        );
    }

    private function createFailureResponse(Throwable $e)
    {
        $this->response = new LogoutResponse();
        if (
            $e->getCode() === AuthenticationManagerInterface::TOO_MANY_REQUESTS_EXCEPTION
        ) {
            $this->response->setStatus(Response::STATUS_TOO_MANY_REQUESTS);
        } else {
            $this->response->setStatus(Response::STATUS_UNKNOWN_ERROR);
        }
        $this->response->setMessage("{$e->getCode()}: {$e->getMessage()}");
        $this->response->setData(
            [
                'forwardTo' => '/index.php',
                'message' => $e->getMessage(),
            ]
        );
    }


}
