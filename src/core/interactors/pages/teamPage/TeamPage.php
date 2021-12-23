<?php

namespace DrlArchive\core\interactors\pages\teamPage;

use DrlArchive\core\classes\Response;
use DrlArchive\core\Exceptions\BadDataException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;

/**
 * @property TeamPageRequest $request
 */
class TeamPage extends Interactor
{

    private TeamRepositoryInterface $teamRepository;

    public function setTeamRepository(TeamRepositoryInterface $repository): void
    {
        $this->teamRepository = $repository;
    }

    public function execute(): void
    {
        try {
            $this->getUserDetails();
            $this->checkForSensibleRequest();
        } catch (\Throwable $e) {
            $this->createFailingResponse($e);
        }
        $this->sendResponse();
    }

    /**
     * @return void
     * @throws BadDataException
     */
    private function checkForSensibleRequest(): void
    {
        if ($this->request->getTeamId() === 0) {
            throw new BadDataException(
                'No team id given'
            );
        }
    }

    private function createFailingResponse(\Throwable $e): void
    {
        if ($e instanceof BadDataException) {
            $status = Response::STATUS_BAD_REQUEST;
        } else {
            $status = Response::STATUS_UNKNOWN_ERROR;
        }
        $message = $e->getMessage();

        $this->response = new TeamPageResponse(
            [
                Response::STATUS => $status,
                Response::MESSAGE => $message,
                Response::LOGGED_IN_USER => $this->loggedInUser,
            ]
        );
    }
}
