<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\viewComposition;

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\CompositionEntity;
use DrlArchive\core\Exceptions\BadDataException;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompositionRepositoryInterface;

/**
 * @property ViewCompositionRequest $request
 */
class ViewComposition extends Interactor
{

    private CompositionRepositoryInterface $compositionRepository;
    private CompositionEntity $composition;

    public function setCompositionRepository(
        CompositionRepositoryInterface $create
    ): void {
        $this->compositionRepository = $create;
    }

    public function execute(): void
    {
        try {
            $this->getUserDetails();
            $this->checkRequestHasId();
            $this->fetchCompositionDetails();
            $this->fetchChanges();
            $this->createResponse();
        } catch (\Throwable $e) {
            $this->createFailureResponse($e);
        }
        $this->sendResponse();
    }

    /**
     * @throws BadDataException
     */
    private function checkRequestHasId(): void
    {
        if ($this->request->getCompositionId() === 0) {
            throw new BadDataException(
                'No composition id given'
            );
        }
    }

    private function createFailureResponse(\Throwable $e): void
    {
        if ($e instanceof BadDataException) {
            $status = Response::STATUS_BAD_REQUEST;
        } else {
            $status = Response::STATUS_UNKNOWN_ERROR;
        }

        $this->response = new ViewCompositionResponse(
            [
                Response::STATUS => $status,
                Response::MESSAGE => $e->getMessage()
            ]
        );
    }

    /**
     * @throws CleanArchitectureException
     */
    private function fetchCompositionDetails(): void
    {
        $this->composition = $this->compositionRepository
            ->fetchCompositionById(
                $this->request->getCompositionId()
            );
    }

    /**
     * @throws CleanArchitectureException
     */
    private function fetchChanges(): void
    {
        $this->compositionRepository->fetchChangesByComposition(
            $this->composition
        );
    }

    private function createResponse(): void
    {
        $data = [
            ViewCompositionResponse::DATA_COMPOSITION_NAME =>
                $this->composition->getName(),
            ViewCompositionResponse::DATA_NUMBER_OF_CHANGES =>
                count($this->composition->getChanges()),
            ViewCompositionResponse::DATA_CHANGES => [],
        ];

        foreach ($this->composition->getChanges() as $change) {
            if ($this->request->isUpChanges()) {
                $changeToAdd = sprintf(
                    '%d - %d',
                    $change->getUpBell(),
                    $change->getDownBell()
                );
            } else {
                $changeToAdd = sprintf(
                    '%d - %s',
                    $change->getDownBell(),
                    $change->getBellToFollow() === 0
                        ? 'Lead'
                        : (string)$change->getBellToFollow(),
                );
            }
            $data[ViewCompositionResponse::DATA_CHANGES][] = [
                ViewCompositionResponse::DATA_CHANGE_NUMBER =>
                    $change->getChangeNumber(),
                ViewCompositionResponse::DATA_CHANGE_TEXT =>
                    $changeToAdd,
            ];
        }

        $this->response = new ViewCompositionResponse(
            [
                Response::STATUS => Response::STATUS_SUCCESS,
                Response::DATA => $data,
            ]
        );
    }
}
