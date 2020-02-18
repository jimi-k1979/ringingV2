<?php
declare(strict_types=1);

namespace core\interactors\competition\createCompetition;

use DrlArchive\core\interactors\competition\createCompetition\CreateCompetition;
use DrlArchive\core\interactors\competition\createCompetition\CreateCompetitionRequest;
use DrlArchive\core\interactors\Interactor;
use mocks\PreseenterDummy;
use mocks\TransactionManagerDummy;
use PHPUnit\Framework\TestCase;

class CreateCompetitionTest extends TestCase
{
    public function testInstantiation(): void
    {
        $request = new CreateCompetitionRequest([

        ]);

        $useCase = new CreateCompetition();
        $useCase->setRequest($request);
        $useCase->setPresenter(new PreseenterDummy());
        $useCase->setCompetitionRepository(new CompetitionDummy());
        $useCase->setTransactionManager(new TransactionManagerDummy());

        $this->assertInstanceOf(
            Interactor::class,
            $useCase
        );
    }
}
