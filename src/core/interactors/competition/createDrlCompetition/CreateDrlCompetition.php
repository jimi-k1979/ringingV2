<?php
declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\createDrlCompetition;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DrlCompetitionEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\managers\TransactionManagerInterface;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\core\interfaces\repositories\SecurityRepositoryInterface;
use Exception;

/**
 * Class CreateDrlCompetition
 * @package DrlArchive\core\interactors\competition\createDrlCompetition
 * @property CreateDrlCompetitionRequest $request
 */
class CreateDrlCompetition extends Interactor
{

    private CompetitionRepositoryInterface $competitionRepository;
    private TransactionManagerInterface $transactionManager;
    private DrlCompetitionEntity $competitionEntity;

    /**
     * @param CompetitionRepositoryInterface $competitionRepository
     */
    public function setCompetitionRepository(
        CompetitionRepositoryInterface $competitionRepository
    ): void {
        $this->competitionRepository = $competitionRepository;
    }

    /**
     * @param TransactionManagerInterface $transactionManager
     */
    public function setTransactionManager(
        TransactionManagerInterface $transactionManager
    ): void {
        $this->transactionManager = $transactionManager;
    }

    public function execute(): void
    {
        $this->checkUserIsAuthorised(
            SecurityRepositoryInterface::ADD_NEW_PERMISSION
        );
        try {
            $this->transactionManager->startTransaction();
            $this->createEntity();
            $this->writeToDatabase();
            $this->createResponse();
            $this->transactionManager->commitTransaction();
        } catch (Exception $e) {
            $this->transactionManager->rollbackTransaction();
            $this->createFailingResponse($e);
        }

        $this->presenter->send($this->response);
    }

    private function createEntity(): void
    {
        $this->competitionEntity = new DrlCompetitionEntity();
        $this->competitionEntity->setName(
            $this->request->getCompetitionName()
        );
        $this->competitionEntity->setSingleTowerCompetition(
            $this->request->getSingleTower()
        );
    }

    private function writeToDatabase(): void
    {
        $this->competitionRepository->insertDrlCompetition(
            $this->competitionEntity
        );
    }

    private function createResponse(): void
    {
        $this->response = new CreateDrlCompetitionResponse(
            [
                Response::STATUS => Response::STATUS_SUCCESS,
                Response::MESSAGE => 'Competition created successfully',
                Response::DATA => [
                    CreateDrlCompetitionResponse::DATA_ID =>
                        $this->competitionEntity->getId(),
                    CreateDrlCompetitionResponse::DATA_NAME =>
                        $this->competitionEntity->getName(),
                    CreateDrlCompetitionResponse::DATA_SINGLE_TOWER =>
                        $this->competitionEntity->isSingleTowerCompetition(),
                ],
            ]);
    }

    private function createFailingResponse(Exception $e): void
    {
        $this->response = new CreateDrlCompetitionResponse(
            [
                Response::STATUS => Response::STATUS_NOT_CREATED,
                Response::MESSAGE => 'Unable to create competition',
                Response::DATA => [
                    Response::DATA_MESSAGE =>
                        $e->getMessage(),
                    Response::DATA_CODE =>
                        $e->getCode(),
                ],
            ]
        );
    }

}
