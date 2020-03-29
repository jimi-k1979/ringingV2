<?php
declare(strict_types=1);

namespace DrlArchive\core\interactors\team\CreateTeam;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\DeaneryRepositoryInterface;
use DrlArchive\core\interfaces\repositories\SecurityRepositoryInterface;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;
use DrlArchive\core\interfaces\repositories\TransactionManagerInterface;
use Exception;

class CreateTeam extends Interactor
{

    /**
     * @var TeamRepositoryInterface
     */
    private $teamRepository;
    /**
     * @var DeaneryRepositoryInterface
     */
    private $deaneryRepository;
    /**
     * @var TransactionManagerInterface
     */
    private $transactionManager;
    /**
     * @var TeamEntity
     */
    private $teamEntity;

    /**
     * @param TeamRepositoryInterface $teamRepository
     */
    public function setTeamRepository(
        TeamRepositoryInterface $teamRepository
    ): void {
        $this->teamRepository = $teamRepository;

    }

    /**
     * @param DeaneryRepositoryInterface $deaneryRepository
     */
    public function setDeaneryRepository(
        DeaneryRepositoryInterface $deaneryRepository
    ): void {
        $this->deaneryRepository = $deaneryRepository;
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

        $this->sendResponse();
    }

    private function createEntity(): void
    {
        $this->teamEntity = new TeamEntity();
        $this->teamEntity->setName(
            $this->request->getName()
        );
        $this->teamEntity->setDeanery(
            $this->deaneryRepository->selectDeanery(
                $this->request->getDeanery()
            )
        );
    }

    private function writeToDatabase(): void
    {
        $this->teamEntity = $this->teamRepository
            ->insertTeam($this->teamEntity);
    }

    private function createResponse(): void
    {
        $this->response = new CreateTeamResponse([
            Response::RESPONSE_STATUS => Response::STATUS_SUCCESS,
            Response::RESPONSE_MESSAGE => 'Team created successfully',
            Response::RESPONSE_DATA => [
                'id' => $this->teamEntity->getId(),
                'name' => $this->teamEntity->getName(),
                'deanery' => $this->teamEntity->getDeanery()->getName(),
            ],
        ]);
    }

    private function createFailingResponse(Exception $e): void
    {
        $this->response = new CreateTeamResponse([
            Response::RESPONSE_STATUS => Response::STATUS_NOT_CREATED,
            Response::RESPONSE_MESSAGE => 'Team not created',
            Response::RESPONSE_DATA => [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ],
        ]);
    }


}