<?php
declare(strict_types=1);

namespace DrlArchive\core\interactors\team\CreateTeam;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\DeaneryEntity;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\DeaneryRepositoryInterface;
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
     * @var DeaneryEntity
     */
    private $deaneryEntity;
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
        try {
            $this->transactionManager->startTransaction();
            $this->fetchDeaneryEntity();
            $this->createTeamEntity();
            $this->createResponse();
            $this->transactionManager->commitTransaction();
        } catch (Exception $e) {
            $this->transactionManager->rollbackTransaction();
            $this->createFailingResponse();
        }

        $this->sendResponse();
    }

    private function fetchDeaneryEntity()
    {
        $this->deaneryEntity = $this->deaneryRepository->getDeaneryByName(
            $this->request->getDeanery()
        );
    }

    private function createTeamEntity()
    {
        $teamEntity = new TeamEntity();
        $teamEntity->setName(
            $this->request->getName()
        );
        $teamEntity->setDeanery(
            $this->deaneryEntity
        );

        $this->teamEntity = $this->teamRepository->insertTeam($teamEntity);

    }

    private function createResponse()
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

    private function createFailingResponse()
    {

    }


}