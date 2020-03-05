<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\drlCompetitionFuzzySearch;


use DrlArchive\core\classes\Response;
use  DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use Exception;

class DrlCompetitionFuzzySearch extends Interactor
{

    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;
    /**
     * @var DrlCompetitionEntity[]
     */
    private $foundCompetitions;

    /**
     * @param CompetitionRepositoryInterface $repository
     */
    public function setCompetitionRepository(
        CompetitionRepositoryInterface $repository
    ): void {
        $this->competitionRepository = $repository;
    }

    public function execute(): void
    {
        try {
            $this->searchForCompetitions();
            $this->createSuccessfulResponse();
        } catch (Exception $e) {
            $this->createFailingResponse($e);
        }

        $this->sendResponse();
    }

    private function searchForCompetitions()
    {
        $this->foundCompetitions = $this->competitionRepository
            ->fuzzySearchDrlCompetition($this->request->getSearchTerm());
    }

    private function createSuccessfulResponse()
    {
        $dataArray = [];
        foreach ($this->foundCompetitions as $competition) {
            $dataArray[] = [
                'id' => $competition->getId(),
                'name' => $competition->getName(),
            ];
        }

        $this->response = new DrlCompetitionFuzzySearchResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
                Response::RESPONSE_MESSAGE => 'Success',
                Response::RESPONSE_DATA => $dataArray,
            ]
        );
    }

    private function createFailingResponse(Exception $e)
    {
        $this->response = new DrlCompetitionFuzzySearchResponse(
            [
                Response::RESPONSE_STATUS => Response::STATUS_NOT_FOUND,
                Response::RESPONSE_MESSAGE => 'No competitions found',
            ]
        );
    }

}