<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\fetchDrlCompetitionById;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\core\interfaces\repositories\LocationRepositoryInterface;
use Exception;

/**
 * Class FetchDrlCompetitionById
 * @package DrlArchive\core\interactors\competition\fetchDrlCompetitionById
 * @property FetchDrlCompetitionByIdRequest $request
 */
class FetchDrlCompetitionById extends Interactor
{

    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;
    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;
    /**
     * @var DrlCompetitionEntity
     */
    private $competition;

    public function setCompetitionRepository(
        CompetitionRepositoryInterface $repository
    ): void {
        $this->competitionRepository = $repository;
    }

    public function setLocationRepository(
        LocationRepositoryInterface $repository
    ): void {
        $this->locationRepository = $repository;
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
        $this->competition = $this->competitionRepository
            ->selectDrlCompetition($this->request->getCompetitionId());
        if ($this->competition->isSingleTowerCompetition()) {
            $this->competition->setUsualLocation(
                $this->locationRepository->fetchLocationById(
                    $this->competition->getUsualLocation()->getId()
                )
            );
        }
    }

    private function createResponse(): void
    {
        $this->response = new FetchDrlCompetitionByIdResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
                Response::RESPONSE_DATA => [
                    'competition' => $this->competition,
                ],
            ]
        );
    }

    private function createFailureResponse(Exception $e): void
    {
        $this->response = new FetchDrlCompetitionByIdResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_NOT_FOUND,
                Response::RESPONSE_MESSAGE => 'Competition not found',
                Response::RESPONSE_DATA => [
                    'code' => $e->getCode(),
                ]
            ]
        );
    }

}