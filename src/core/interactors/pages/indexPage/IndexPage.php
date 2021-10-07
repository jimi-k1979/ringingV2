<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\indexPage;


use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;

/**
 * Class IndexPage
 * @package DrlArchive\core\interactors\pages\indexPage
 */
class IndexPage extends Interactor
{

    public function execute(): void
    {
        $this->getUserDetails();
        $this->createResponse();
        $this->sendResponse();
    }

    private function createResponse()
    {
        $this->response = new IndexPageResponse();
        $this->response->setStatus(Response::STATUS_SUCCESS);
        $this->response->setLoggedInUser($this->loggedInUser);
    }

}
