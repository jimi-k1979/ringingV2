<?php

namespace DrlArchive\core\interactors\pages\teamPage;

use DrlArchive\core\classes\Response;
use DrlArchive\core\Constants;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\Exceptions\BadDataException;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;
use DrlArchive\core\StatFieldNames;

use function Webmozart\Assert\Tests\StaticAnalysis\null;

/**
 * @property TeamPageRequest $request
 */
class TeamPage extends Interactor
{

    private TeamRepositoryInterface $teamRepository;
    private TeamEntity $team;
    private array $statistics = [];
    private array $results = [];
    private int $earliestYear = Constants::MINIMUM_YEAR;
    private int $latestYear = Constants::MINIMUM_YEAR;

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
        if ($this->team->getEarliestYear() === null) {
            $this->request->setShowStats(false);
            $this->request->setShowResults(false);
        }

        if (
            $this->team->getEarliestYear() !== null
            &&
            $this->request->getStartYear() < $this->team->getEarliestYear()
        ) {
            $this->earliestYear = $this->team->getEarliestYear();
        } else {
            $this->earliestYear = $this->request->getStartYear();
        }

        if (
            $this->team->getLatestYear() !== null
            && (
                $this->request->getEndYear() > $this->team->getLatestYear()
                || $this->request->getEndYear() === null
            )
        ) {
            $this->latestYear = $this->team->getLatestYear();
        } elseif (
            $this->request->getEndYear() === null
        ) {
            $this->latestYear = (int)date('Y');
        } else {
            $this->latestYear = $this->request->getEndYear();
        }
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
                $this->earliestYear,
                $this->latestYear
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
                $this->earliestYear,
                $this->latestYear
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
                ucwords($this->team->getDeanery()->getRegion()),
            TeamPageResponse::DATA_TEAM_EARLIEST_YEAR =>
                $this->team->getEarliestYear(),
            TeamPageResponse::DATA_TEAM_MOST_RECENT_YEAR =>
                $this->team->getLatestYear(),
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
