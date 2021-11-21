<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\compositionPage;

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\CompositionEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompositionRepositoryInterface;

class CompositionPage extends Interactor
{

    public const SHORT_LENGTH = 'short';
    public const MEDIUM_LENGTH = 'medium';
    public const LONG_LENGTH = 'long';

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
            $changeCount = count($composition->getChanges());
            if ($changeCount <= 40) {
                $length = self::SHORT_LENGTH;
            } elseif ($changeCount <= 80) {
                $length = self::MEDIUM_LENGTH;
            } else {
                $length = self::LONG_LENGTH;
            }

            $data[] = [
                CompositionPageResponse::DATA_COMPOSITION_ID =>
                    $composition->getId(),
                CompositionPageResponse::DATA_COMPOSITION =>
                    $composition->getName(),
                CompositionPageResponse::DATA_BELLS =>
                    $composition->getNumberOfBells(),
                CompositionPageResponse::DATA_TENOR =>
                    $composition->isTenorTurnedIn() ? "true" : "false",
                CompositionPageResponse::DATA_DESCRIPTION =>
                    $composition->getDescription(),
                CompositionPageResponse::DATA_LENGTH =>
                    $length,
            ];
        }
        $this->response = new CompositionPageResponse();
        $this->response->setStatus(Response::STATUS_SUCCESS);
        $this->response->setLoggedInUser($this->loggedInUser);
        $this->response->setData($data);
    }
}
