<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\team\TeamFuzzySearch;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;
use Exception;

/**
 * Class TeamFuzzySearch
 * @package DrlArchive\core\interactors\team\TeamFuzzySearch
 * @property TeamFuzzySearchRequest $request
 */
class TeamFuzzySearch extends Interactor
{

    private TeamRepositoryInterface $teamRepository;
    /**
     * @var TeamEntity[]
     */
    private array $teams;

    /**
     * @param TeamRepositoryInterface $repository
     */
    public function setTeamRepository(TeamRepositoryInterface $repository): void
    {
        $this->teamRepository = $repository;
    }

    public function execute(): void
    {
        $this->checkUserIsAuthorised();
        try {
            $this->fetchTeams();
            $this->createResponse();
        } catch (Exception $e) {
            $this->createEmptyResponse();
        }

        $this->sendResponse();
    }

    private function fetchTeams(): void
    {
        $this->teams = $this->teamRepository->fuzzySearchTeam(
            $this->request->getSearchTerm()
        );
    }

    private function createResponse(): void
    {
        $responseArray = [];
        foreach ($this->teams as $currentTeam) {
            $responseArray[] = [
                TeamFuzzySearchResponse::DATA_ID =>
                    $currentTeam->getId(),
                TeamFuzzySearchResponse::DATA_NAME =>
                    $currentTeam->getName(),
            ];
        }
        $this->response = new TeamFuzzySearchResponse(
            [
                Response::STATUS => Response::STATUS_SUCCESS,
                Response::DATA => $responseArray,
            ]
        );
    }

    private function createEmptyResponse()
    {
        $this->response = new TeamFuzzySearchResponse(
            [
                Response::STATUS => Response::STATUS_SUCCESS,
                Response::DATA => [],
            ]
        );
    }
}
