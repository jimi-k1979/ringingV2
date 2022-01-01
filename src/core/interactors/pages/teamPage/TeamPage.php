<?php

namespace DrlArchive\core\interactors\pages\teamPage;

use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\Exceptions\BadDataException;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;
use DrlArchive\core\StatFieldNames;

/**
 * @property TeamPageRequest $request
 */
class TeamPage extends Interactor
{

    private TeamRepositoryInterface $teamRepository;
    private TeamEntity $team;
    private array $statistics = [];
    private array $results = [];

    public function setTeamRepository(TeamRepositoryInterface $repository): void
    {
        $this->teamRepository = $repository;
    }

    public function execute(): void
    {
        try {
            $this->getUserDetails();
            $this->checkRequestData();
            $this->fetchTeamData();
            $this->fetchTeamStatistics();
            $this->fetchTeamResults();
            $this->createResponse();
        } catch (\Throwable $e) {
            $this->createFailingResponse($e);
        }
        $this->sendResponse();
    }

    /**
     * @return void
     * @throws BadDataException
     */
    private function checkRequestData(): void
    {
        if ($this->request->getTeamId() === 0) {
            throw new BadDataException(
                'No team id given'
            );
        }
    }

    /**
     * @return void
     * @throws CleanArchitectureException
     */
    private function fetchTeamData(): void
    {
        $this->team = $this->teamRepository->fetchTeamById(
            $this->request->getTeamId()
        );
    }

    /**
     * @return void
     * @throws CleanArchitectureException
     */
    private function fetchTeamStatistics(): void
    {
        if ($this->request->isShowStats()) {
            $this->statistics = $this->teamRepository->fetchTeamStatistics(
                $this->team,
                $this->request->getStatsOptions()[StatFieldNames::STATS_START_YEAR],
                $this->request->getStatsOptions()[StatFieldNames::STATS_END_YEAR]
            );
        }
    }

    /**
     * @return void
     * @throws CleanArchitectureException
     */
    private function fetchTeamResults(): void
    {
        if ($this->request->isShowResults()) {
            $this->results = $this->teamRepository->fetchTeamResults(
                $this->team,
                $this->request->getStatsOptions()[StatFieldNames::STATS_START_YEAR],
                $this->request->getStatsOptions()[StatFieldNames::STATS_END_YEAR]
            );
        }
    }

    private function createResponse(): void
    {
        $team = [
            TeamPageResponse::DATA_TEAM_ID =>
                $this->team->getId(),
            TeamPageResponse::DATA_TEAM_NAME =>
                $this->team->getName(),
            TeamPageResponse::DATA_TEAM_DEANERY =>
                $this->team->getDeanery()->getName(),
            TeamPageResponse::DATA_TEAM_REGION =>
                $this->team->getDeanery()->getRegion(),
        ];

        $this->response = new TeamPageResponse();
        $this->response->setData(
            [
                TeamPageResponse::DATA_TEAM => $team,
                TeamPageResponse::DATA_STATS => $this->statistics,
                TeamPageResponse::DATA_RESULTS => $this->results,
                TeamPageResponse::DATA_STATS_OPTIONS =>
                    $this->request->getStatsOptions(),
            ]
        );
        $this->response->setLoggedInUser($this->loggedInUser);
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
