<?php
declare(strict_types=1);

namespace DrlArchive\core\interactors\team\CreateTeam;


use DrlArchive\core\classes\Response;
use DrlArchive\core\entities\TeamEntity;
use DrlArchive\core\Exceptions\AccessDeniedException;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\managers\TransactionManagerInterface;
use DrlArchive\core\interfaces\repositories\DeaneryRepositoryInterface;
use DrlArchive\core\interfaces\repositories\SecurityRepositoryInterface;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;
use Exception;

#use Exception;

/**
 * Class CreateTeam
 * @package DrlArchive\core\interactors\team\CreateTeam
 * @property CreateTeamRequest $request
 */
class CreateTeam extends Interactor
{

    private TeamRepositoryInterface $teamRepository;
    private DeaneryRepositoryInterface $deaneryRepository;
    private TransactionManagerInterface $transactionManager;
    private TeamEntity $teamEntity;

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

    /**
     * @throws AccessDeniedException
     */
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
        $this->teamRepository->insertTeam($this->teamEntity);
    }

    private function createResponse(): void
    {
        $this->response = new CreateTeamResponse([
                                                     Response::STATUS => Response::STATUS_SUCCESS,
                                                     Response::MESSAGE => 'Team created successfully',
                                                     Response::DATA => [
                                                         CreateTeamResponse::DATA_ID =>
                                                             $this->teamEntity->getId(),
                                                         CreateTeamResponse::DATA_NAME =>
                                                             $this->teamEntity->getName(),
                                                         CreateTeamResponse::DATA_DEANERY =>
                                                             $this->teamEntity->getDeanery()->getName(),
                                                     ],
                                                 ]);
    }

    private function createFailingResponse(Exception $e): void
    {
        $this->response = new CreateTeamResponse([
                                                     Response::STATUS => Response::STATUS_NOT_CREATED,
                                                     Response::MESSAGE => 'Team not created',
                                                     Response::DATA => [
                                                         Response::DATA_MESSAGE =>
                                                             $e->getMessage(),
                                                         Response::DATA_CODE =>
                                                             $e->getCode(),
                                                     ],
                                                 ]);
    }


}
