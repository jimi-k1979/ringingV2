<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\compositionPage;

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\CompositionEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompositionRepositoryInterface;

class CompositionPage extends Interactor
{

    private CompositionRepositoryInterface $compositionRepository;
    /**
     * @var CompositionEntity[]
     */
    private array $compositions;

    public function setCompositionRepository(
        CompositionRepositoryInterface $repository
    ): void {
        $this->compositionRepository = $repository;
    }

    public function execute(): void
    {
        try {
            $this->getUserDetails();
            $this->fetchCompositionList();
            $this->createResponse();
        } catch (\Throwable $e) {
        }
        $this->sendResponse();
    }

    private function fetchCompositionList(): void
    {
        $this->compositions = $this->compositionRepository
            ->fetchAllCompositions();
    }

    private function createResponse(): void
    {
        $data = [];
        foreach ($this->compositions as $composition) {
            $data[] = [
                CompositionPageResponse::DATA_COMPOSITION_ID =>
                    $composition->getId(),
                CompositionPageResponse::DATA_COMPOSITION =>
                    $composition->getName(),
                CompositionPageResponse::DATA_BELLS =>
                    $composition->getNumberOfBells(),
                CompositionPageResponse::DATA_TENOR =>
                    $composition->isTenorTurnedIn(),
            ];
        }
        $this->response = new CompositionPageResponse();
        $this->response->setStatus(Response::STATUS_SUCCESS);
        $this->response->setLoggedInUser($this->loggedInUser);
        $this->response->setData($data);
    }
}
