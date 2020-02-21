<?php
declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors;


use DrlArchive\core\classes\Request;
use DrlArchive\core\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interactors\competition\createCompetition\CreateCompetition;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;
use DrlArchive\implementation\factories\managers\TransactionManagerFactory;
use DrlArchive\implementation\factories\repositories\CompetitionRepositoryFactory;

class CreateCompetitionFactory implements InteractorFactoryInterface
{

    public function create(PresenterInterface $presenter, ?Request $request = null): InteractorInterface
    {
        $useCase = new CreateCompetition();
        $useCase->setRequest($request);
        $useCase->setPresenter($presenter);
        $useCase->setCompetitionRepository((new CompetitionRepositoryFactory())->create());
        $useCase->setTransactionManager((new TransactionManagerFactory())->create());
        return $useCase;

    }
}