<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\resultsArchive;


use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;

class ResultsArchive extends Interactor
{

    public function execute(): void
    {
        $this->getUserDetails();
        $this->createResponse();
        $this->sendResponse();
    }

    private function createResponse()
    {
        $this->response = new ResultsArchiveResponse();
        $this->response->setStatus(Response::STATUS_SUCCESS);
        $this->response->setLoggedInUser($this->loggedInUser);
    }
}
