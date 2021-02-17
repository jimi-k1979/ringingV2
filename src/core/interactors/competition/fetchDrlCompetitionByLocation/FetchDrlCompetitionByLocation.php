<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\fetchDrlCompetitionByLocation;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlCompetitionEntity;
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
        $this->checkUserIsAuthorised();

        try {
            $this->fetchData();
            $this->createResponse();
        } catch (Exception $e) {
            $this->createFailureResponse($e);
        }

        $this->sendResponse();
    }

    private function fetchData(): void
    {
        $this->queryData = $this->competitionRepository
            ->fetchDrlCompetitionByLocationId(
                $this->request->getLocationId()
            );
    }

    private function createResponse(): void
    {
        $dataArray = [];
        foreach ($this->queryData as $competition) {
            $dataArray[] = [
                'id' => $competition->getId(),
                'text' => $competition->getName(),
            ];
        }

        $this->response = new FetchDrlCompetitionByLocationResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
                Response::RESPONSE_DATA => $dataArray,
            ]
        );
    }

    private function createFailureResponse(Exception $e): void
    {
        $this->response = new FetchDrlCompetitionByLocationResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_NOT_FOUND,
                Response::RESPONSE_MESSAGE => 'No events found',
                Response::RESPONSE_DATA => [
                    'code' => $e->getCode(),
                ]
            ]
        );
    }

}