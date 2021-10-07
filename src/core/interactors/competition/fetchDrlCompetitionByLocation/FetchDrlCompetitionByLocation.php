<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\fetchDrlCompetitionByLocation;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\entities\LocationEntity;
use DrlArchive\core\Exceptions\CleanArchitectureException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use Exception;

/**
 * Class FetchDrlCompetitionByLocation
 * @package DrlArchive\core\interactors\competition\fetchDrlCompetitionByLocation
 * @property FetchDrlCompetitionByLocationRequest $request
 */
class FetchDrlCompetitionByLocation extends Interactor
{

    private CompetitionRepositoryInterface $competitionRepository;
    /**
     * @var DrlCompetitionEntity[]
     */
    private array $queryData;

    public function setCompetitionRepository(
        CompetitionRepositoryInterface $repository
    ): void {
        $this->competitionRepository = $repository;
    }

    public function execute(): void
    {

        try {
            $this->checkUserIsAuthorised();
            $this->fetchData();
            $this->createResponse();
        } catch (Exception $e) {
            $this->createFailureResponse($e);
        }

        $this->sendResponse();
    }

    /**
     * @throws CleanArchitectureException
     */
    private function fetchData(): void
    {
        $location = new LocationEntity();
        $location->setLocation($this->request->getLocationName());
        $this->queryData = $this->competitionRepository
            ->fetchDrlCompetitionByLocation($location);
    }

    private function createResponse(): void
    {
        $dataArray = [];
        foreach ($this->queryData as $competition) {
            $dataArray[] = [
                FetchDrlCompetitionByLocationResponse::DATA_ID =>
                    $competition->getId(),
                FetchDrlCompetitionByLocationResponse::DATA_TEXT =>
                    $competition->getName(),
            ];
        }

        $this->response = new FetchDrlCompetitionByLocationResponse(
            [
                Response::STATUS => Response::STATUS_SUCCESS,
                Response::DATA => $dataArray,
            ]
        );
    }

    private function createFailureResponse(Exception $e): void
    {
        $this->response = new FetchDrlCompetitionByLocationResponse(
            [
                Response::STATUS => Response::STATUS_NOT_FOUND,
                Response::MESSAGE => 'No events found',
                Response::DATA => [
                    Response::DATA_CODE => $e->getCode(),
                ]
            ]
        );
    }

}
