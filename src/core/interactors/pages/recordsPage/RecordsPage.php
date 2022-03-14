<?php

namespace DrlArchive\core\interactors\pages\recordsPage;

use DrlArchive\core\classes\Response;
use DrlArchive\core\Constants;
use DrlArchive\core\entities\DrlEventEntity;
use DrlArchive\core\entities\RecordRequestOptionsEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\PagesRepositoryInterface;

class RecordsPage extends Interactor
{
    private PagesRepositoryInterface $pageRepository;
    private array $listOfRecords;

    /**
     * @param PagesRepositoryInterface $pageRepository
     */
    public function setPageRepository(
        PagesRepositoryInterface $pageRepository
    ): void {
        $this->pageRepository = $pageRepository;
    }

    public function execute(): void
    {
        try {
            $this->getUserDetails();
            $this->fetchEventRecords();
            $this->createResponse();
        } catch (\Throwable $e) {
            $this->createFailureResponse();
        }
        $this->sendResponse();
    }

    /**
     * @return void
     * @throws CleanArchitectureException
     */
    private function fetchEventRecords(): void
    {
        $this->listOfRecords = $this->pageRepository->fetchRecordsPageList();
    }

    private function createResponse(): void
    {
        $this->response = new RecordsPageResponse();
        $this->response->setStatus(Response::STATUS_SUCCESS);
        $this->response->setData($this->listOfRecords);
    }

    private function createFailureResponse(): void
    {
        $this->response = new RecordsPageResponse();
        $this->response->setStatus(Response::STATUS_UNKNOWN_ERROR);
        $this->response->setMessage('Unknown error');
    }
}
