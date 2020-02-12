<?php
declare(strict_types=1);

namespace DrlArchive\core\interactors;


use DrlArchive\core\classes\Request;
use DrlArchive\core\classes\Response;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;

abstract class Interactor implements InteractorInterface
{
    /**
     * @var Response|null
     */
    protected $response;
    /**
     * @var PresenterInterface|null
     */
    protected $presenter;
    /**
     * @var Request|null
     */
    protected $request;

    /**
     * @param PresenterInterface|null $presenter
     */
    public function setPresenter(?PresenterInterface $presenter): void
    {
        $this->presenter = $presenter;
    }

    /**
     * @param Request|null $request
     */
    public function setRequest(?Request $request): void
    {
        $this->request = $request;
    }

    protected function sendResponse(): void
    {
        $this->presenter->send($this->response);
    }

}