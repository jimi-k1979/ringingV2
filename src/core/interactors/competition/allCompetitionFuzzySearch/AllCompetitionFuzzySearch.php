<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\allCompetitionFuzzySearch;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\AbstractCompetitionEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use Exception;

/**
 * Class AllCompetitionFuzzySearch
 * @package DrlArchive\core\interactors\competition\allCompetitionFuzzySearch
 * @property AllCompetitionFuzzySearchRequest $request
 */
class AllCompetitionFuzzySearch extends Interactor
{
    /**
     * @var CompetitionRepositoryInterface
     */
    private CompetitionRepositoryInterface $competitionRepository;
    /**
     * @var AbstractCompetitionEntity[]
     */
    private array $data;

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
            $this->createSuccessfulResponse();
        } catch (Exception $e) {
            $this->createFailureResponse($e);
        }
        $this->sendResponse();
    }

    private function fetchData(): void
    {
        $this->data = $this->competitionRepository
            ->fuzzySearchAllCompetitions(
                $this->request->getSearchTerm()
            );
    }

    private function createSuccessfulResponse(): void
    {
        $dataArray = [];
        foreach ($this->data as $datum) {
            $dataArray[] = [
                AllCompetitionFuzzySearchResponse::DATA_ID =>
                    $datum->getId(),
                AllCompetitionFuzzySearchResponse::DATA_NAME =>
                    $datum->getName(),
            ];
        }

        $this->response = new AllCompetitionFuzzySearchResponse(
            [
                Response::STATUS => Response::STATUS_SUCCESS,
                Response::DATA => $dataArray,
            ]
        );
    }

    private function createFailureResponse(Exception $e): void
    {
        $this->response = new AllCompetitionFuzzySearchResponse(
            [
                Response::STATUS => Response::STATUS_NOT_FOUND,
                Response::MESSAGE => 'No competitions found',
                Response::DATA => [
                    Response::DATA_CODE =>
                        $e->getCode(),
                ],
            ]
        );
    }

}
