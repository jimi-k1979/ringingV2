<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\fetchDrlCompetitionByName;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use Throwable;

/**
 * Class FetchDrlCompetitionByName
 * @package DrlArchive\core\interactors\competition\fetchDrlCompetitionByName
 * @property FetchDrlCompetitionByNameRequest $request
 */
class FetchDrlCompetitionByName extends Interactor
{
    private CompetitionRepositoryInterface $competitionRepository;
    private DrlCompetitionEntity $competition;

    public function execute(): void
    {
        try {
            $this->checkUserIsAuthorised();
            $this->fetchData();
            $this->createSuccessfulResponse();
        } catch (Throwable $e) {
            $this->createFailureResponse($e);
        }

        $this->sendResponse();
    }

    public function setCompetitionRepository(
        CompetitionRepositoryInterface $repository
    ): void {
        $this->competitionRepository = $repository;
    }

    private function fetchData(): void
    {
        $this->competition = $this->competitionRepository
            ->fetchDrlCompetitionByName(
                $this->request->getCompetitionName()
            );
    }

    private function createSuccessfulResponse(): void
    {
        $this->response = new FetchDrlCompetitionByNameResponse();
        $this->response->setStatus(Response::STATUS_SUCCESS);
        $this->response->setData(
            $this->competition->toArray()
        );
    }

    private function createFailureResponse(Throwable $e): void
    {
        $this->response = new FetchDrlCompetitionByNameResponse();
        if (
            $e->getCode() === CompetitionRepositoryInterface::NO_ROWS_FOUND_EXCEPTION
        ) {
            $this->response->setStatus(Response::STATUS_NOT_FOUND);
        } else {
            $this->response->setStatus(Response::STATUS_UNKNOWN_ERROR);
        }
        $this->response->setMessage("{$e->getCode()}: {$e->getMessage()}");
    }
}
