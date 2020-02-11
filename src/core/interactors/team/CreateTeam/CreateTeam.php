<?php
declare(strict_types=1);

namespace DrlArchive\core\interactors\team\CreateTeam;


use DrlArchive\core\interactors\Interactor;
use DrlArchive\core\interfaces\repositories\DeaneryRepositoryInterface;
use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;
use DrlArchive\core\interfaces\repositories\TransactionManagerInterface;

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


    public function setTeamRepository(
        TeamRepositoryInterface $teamRepository
    ): void {
        $this->teamRepository = $teamRepository;

    }

    public function execute(): void
    {
        // TODO: Implement execute() method.
    }

    public function setDeaneryRepository(
        DeaneryRepositoryInterface $deaneryRepository
    ): void {
        $this->deaneryRepository = $deaneryRepository;
    }

    public function setTransactionManager(
        TransactionManagerInterface $transactionManager
    ): void {
        $this->transactionManager = $transactionManager;
    }

}