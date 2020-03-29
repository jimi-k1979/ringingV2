<?php
declare(strict_types=1);

namespace DrlArchive\core\interactors;


use DrlArchive\core\classes\Request;
use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\UserEntity;
use DrlArchive\core\Exceptions\AccessDeniedException;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\core\interfaces\repositories\SecurityRepositoryInterface;
use DrlArchive\core\interfaces\repositories\UserRepositoryInterface;

abstract class Interactor implements InteractorInterface
{
    /**
     * @var Request|null
     */
    protected $request;
    /**
     * @var Response|null
     */
    protected $response;
    /**
     * @var PresenterInterface|null
     */
    protected $presenter;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var UserEntity
     */
    private $loggedInUser;
    /**
     * @var SecurityRepositoryInterface
     */
    private $securityRepository;

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

    public function setSecurityRepository(SecurityRepositoryInterface $securityRepository): void
    {
        $this->securityRepository = $securityRepository;
    }


    public function setUserRepository(UserRepositoryInterface $userRepository): void
    {
        $this->userRepository = $userRepository;
        $this->loggedInUser = $userRepository->getLoggedInUser();
    }


    protected function checkUserIsAuthorised(?string $permission = null): void
    {
        if (!$this->securityRepository->isUserAuthorised(
            $this->loggedInUser,
            $permission
        )) {
            throw new AccessDeniedException('Not authorised to view this page');
        }
    }

    protected function sendResponse(): void
    {
        $this->presenter->send($this->response);
    }

}