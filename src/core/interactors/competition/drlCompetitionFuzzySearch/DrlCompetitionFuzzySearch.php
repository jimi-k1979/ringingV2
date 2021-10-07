<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\drlCompetitionFuzzySearch;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use Exception;

/**
 * Class DrlCompetitionFuzzySearch
 * @package DrlArchive\core\interactors\competition\drlCompetitionFuzzySearch
 * @property DrlCompetitionFuzzySearchRequest $request
 */
class DrlCompetitionFuzzySearch extends Interactor
{

    private CompetitionRepositoryInterface $competitionRepository;
    /**
     * @var DrlCompetitionEntity[]
     */
    private array $foundCompetitions;

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
            $this->checkUserIsAuthorised();
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
            ->fuzzySearchDrlCompetitions($this->request->getSearchTerm());
    }

    private function createSuccessfulResponse()
    {
        $dataArray = [];
        foreach ($this->foundCompetitions as $competition) {
            $dataArray[] = [
                DrlCompetitionFuzzySearchResponse::DATA_ID =>
                    $competition->getId(),
                DrlCompetitionFuzzySearchResponse::DATA_NAME =>
                    $competition->getName(),
            ];
        }

        $this->response = new DrlCompetitionFuzzySearchResponse(
            [
                Response::STATUS => Response::STATUS_SUCCESS,
                Response::MESSAGE => 'Success',
                Response::DATA => $dataArray,
            ]
        );
    }

    private function createFailingResponse(Exception $e)
    {
        $this->response = new DrlCompetitionFuzzySearchResponse(
            [
                Response::STATUS => Response::STATUS_NOT_FOUND,
                Response::MESSAGE => 'No competitions found',
                Response::DATA => [
                    Response::DATA_CODE => $e->getCode()
                ]
            ]
        );
    }

}