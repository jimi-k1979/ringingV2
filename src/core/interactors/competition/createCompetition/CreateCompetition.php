<?php
declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\createCompetition;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\CompetitionEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\CompetitionRepositoryInterface;
use DrlArchive\core\interfaces\repositories\TransactionManagerInterface;
use Exception;

class CreateCompetition extends Interactor
{

    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;
    /**
     * @var TransactionManagerInterface
     */
    private $transactionManager;
    /**
     * @var CompetitionEntity
     */
    private $competitionEntity;

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

    private function createEntity()
    {
        $this->competitionEntity = new CompetitionEntity();
        $this->competitionEntity->setName(
            $this->request->getCompetitionName()
        );
        $this->competitionEntity->setSingleTowerCompetition(
            $this->request->getSingleTower()
        );

    }

    private function writeToDatabase()
    {
        $this->competitionEntity = $this->competitionRepository
            ->insertCompetition($this->competitionEntity);
    }

    private function createResponse()
    {
        $this->response = new CreateCompetitionResponse([
            Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
            Response::RESPONSE_MESSAGE => 'Competition created successfully',
            Response::RESPONSE_DATA => [
                'id' => $this->competitionEntity->getId(),
                'name' => $this->competitionEntity->getName(),
                'singleTower' => $this->competitionEntity
                    ->isSingleTowerCompetition()
            ],
        ]);
    }

    private function createFailingResponse(Exception $e)
    {
        $this->response = new CreateCompetitionResponse([
            Response::RESPONSE_STATUS => Response::STATUS_NOT_CREATED,
            Response::RESPONSE_MESSAGE => 'Unable to create competition',
            Response::RESPONSE_DATA => [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ],
        ]);
    }

}