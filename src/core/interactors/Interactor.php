<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors;


use DrlArchive\core\classes\Request;
use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\Exceptions\AccessDeniedException;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\managers\AuthenticationManagerInterface;
use DrlArchive\core\interfaces\repositories\SecurityRepositoryInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;

abstract class Interactor implements InteractorInterface
{
    public const ACCESS_DENIED_EXCEPTION_CODE = 9901;
    public const NOT_LOGGED_IN_EXCEPTION_CODE = 9902;

    protected ?Request $request = null;
    protected ?Response $response = null;
    protected ?PresenterInterface $presenter = null;
    protected AuthenticationManagerInterface $authenticationManager;
    /**
     * @var UserEntity
     */
    protected UserEntity $loggedInUser;
    /**
     * @var SecurityRepositoryInterface
     */
    private SecurityRepositoryInterface $securityRepository;

    /**
     * @param Request|null $request
     */
    public function setRequest(?Request $request): void
    {
        $this->request = $request;
    }

    /**
     * @param PresenterInterface|null $presenter
     */
    public function setPresenter(?PresenterInterface $presenter): void
    {
        $this->presenter = $presenter;
    }

    /**
     * @param SecurityRepositoryInterface $securityRepository
     */
    public function setSecurityRepository(
        SecurityRepositoryInterface $securityRepository
    ): void {
        $this->securityRepository = $securityRepository;
    }

    public function setAuthenticationManager(
        AuthenticationManagerInterface $authenticationManager
    ): void {
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * @param UserRepositoryInterface $userRepository
     * @deprecated
     */
    public function setUserRepository(
        UserRepositoryInterface $userRepository
    ): void {
        $this->loggedInUser = $userRepository->getLoggedInUser();
    }

    /**
     * @param string|null $permission
     * @throws AccessDeniedException
     */
    protected function checkUserIsAuthorised(?string $permission = null): void
    {
        $this->loggedInUser = $this->authenticationManager->loggedInUserDetails();
        if (!$this->securityRepository->isUserAuthorised(
            $this->loggedInUser,
            $permission
        )) {
            throw new AccessDeniedException(
                'Not authorised to view this page',
                self::ACCESS_DENIED_EXCEPTION_CODE
            );
        }
    }

    protected function sendResponse(): void
    {
        $this->presenter->send($this->response);
    }

    protected function getUserDetails(): void
    {
        if ($this->authenticationManager->isLoggedIn()) {
            $this->loggedInUser =
                $this->authenticationManager->loggedInUserDetails();
        } else {
            $this->loggedInUser = new UserEntity();
        }
    }
}
