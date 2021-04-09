<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\resultsArchive;

use DrlArchive\core\classes\Response;
use DrlArchive\mocks\PresenterSpy;
use PHPUnit\Framework\TestCase;

class ResultsArchiveTest extends TestCase
{
    public function testSendIsCalled(): void
    {
        $presenter = new PresenterSpy();

        $useCase = new ResultsArchive();
        $useCase->setPresenter($presenter);
        $useCase->execute();

        $this->assertTrue(
            $presenter->hasSendBeenCalled()
        );
    }

    public function testResponse(): void
    {
        $presenter = new PresenterSpy();

        $useCase = new ResultsArchive();
        $useCase->setPresenter($presenter);
        $useCase->execute();

        $response = $presenter->getResponse();

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus()
        );
        $this->assertEmpty(
            $response->getData()
        );
    }

}
