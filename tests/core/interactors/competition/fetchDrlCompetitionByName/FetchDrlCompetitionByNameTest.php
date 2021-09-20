<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\fetchDrlCompetitionByName;

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\Interactor;
use DrlArchive\mocks\AuthenticationManagerDummy;
use DrlArchive\mocks\CompetitionDummy;
use DrlArchive\mocks\CompetitionSpy;
use DrlArchive\mocks\GuestUserDummy;
use DrlArchive\mocks\PresenterDummy;
use DrlArchive\mocks\PresenterSpy;
use DrlArchive\mocks\SecurityRepositoryDummy;
use DrlArchive\mocks\SecurityRepositorySpy;
use DrlArchive\TestConstants;
use DrlArchive\traits\CreateMockDrlCompetitionTrait;
use DrlArchive\traits\CreateMockLocationTrait;
use PHPUnit\Framework\TestCase;

class FetchDrlCompetitionByNameTest extends TestCase
{
    use CreateMockDrlCompetitionTrait;
    use CreateMockLocationTrait;

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new FetchDrlCompetitionByName()
        );
    }

    public function testUserIsAuthorised(): void
    {
        $securitySpy = new SecurityRepositorySpy();

        $useCase = $this->createUseCase();
        $useCase->setSecurityRepository($securitySpy);
        $useCase->execute();

        $this->assertTrue(
            $securitySpy->hasIsUserAuthorisedCalled()
        );
    }

    private function createUseCase(): FetchDrlCompetitionByName
    {
        $request = new FetchDrlCompetitionByNameRequest();
        $request->setCompetitionName('Test competition');

        $entity = new FetchDrlCompetitionByName();
        $entity->setRequest($request);
        $entity->setPresenter(new PresenterDummy());
        $entity->setAuthenticationManager(new AuthenticationManagerDummy());
        $entity->setSecurityRepository(new SecurityRepositoryDummy());
        $entity->setUserRepository(new GuestUserDummy());
        $entity->setCompetitionRepository(new CompetitionDummy());

        return $entity;
    }

    public function testFetchData(): void
    {
        $competitionSpy = new CompetitionSpy();

        $useCase = $this->createUseCase();
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $this->assertTrue(
            $competitionSpy->hasFetchDrlCompetitionByNameBeenCalled()
        );
    }

    public function testSendIsCalled(): void
    {
        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $this->assertTrue(
            $presenterSpy->hasSendBeenCalled()
        );
    }

    public function testSuccessfulResponse(): void
    {
        $presenterSpy = new PresenterSpy();
        $competitionSpy = new CompetitionSpy();
        $competitionSpy->setFetchDrlCompetitionByNameValue(
            $this->createMockDrlCompetition()
        );

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();
        $expectedData = [
            'id' => 999,
            'name' => 'Test competition',
            'isSingleTowerCompetition' => false,
            'usualLocation' => null,
        ];

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus(),
            'Incorrect response status'
        );

        $this->assertEquals(
            $expectedData,
            $response->getData(),
            'Incorrect response data'
        );
    }

    public function testFailingResponse(): void
    {
        $presenterSpy = new PresenterSpy();
        $competitionSpy = new CompetitionSpy();
        $competitionSpy->setFetchDrlCompetitionByNameThrowsException();

        $useCase = $this->createUseCase();
        $useCase->setPresenter($presenterSpy);
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $this->assertEquals(
            Response::STATUS_NOT_FOUND,
            $response->getStatus(),
            'Incorrect response status'
        );
        $this->assertEquals(
            '2402: No competition found',
            $response->getMessage(),
            'Incorrect response message'
        );
        $this->assertEmpty(
            $response->getData(),
            'Incorrect response data'
        );
    }

    public function testSuccessfulFullResponse(): void
    {
        $compEntity = $this->createMockDrlCompetition();
        $compEntity->setSingleTowerCompetition(true);
        $compEntity->setUsualLocation($this->createMockLocation());

        $competitionSpy = new CompetitionSpy();
        $competitionSpy->setFetchDrlCompetitionByNameValue($compEntity);

        $presenterSpy = new PresenterSpy();

        $useCase = $this->createUseCase();
        $useCase->setCompetitionRepository($competitionSpy);
        $useCase->setPresenter($presenterSpy);
        $useCase->execute();

        $response = $presenterSpy->getResponse();

        $expectedData = [
            'id' => TestConstants::TEST_DRL_COMPETITION_ID,
            'name' => TestConstants::TEST_DRL_COMPETITION_NAME,
            'isSingleTowerCompetition' => true,
            'usualLocation' => [
                'id' => TestConstants::TEST_LOCATION_ID,
                'location' => TestConstants::TEST_LOCATION_NAME,
                'deanery' => [
                    'id' => TestConstants::TEST_DEANERY_ID,
                    'name' => TestConstants::TEST_DEANERY_NAME,
                    'locationInCounty' => TestConstants::TEST_DEANERY_REGION,
                ],
                'dedication' => TestConstants::TEST_LOCATION_DEDICATION,
                'numberOfBells' => TestConstants::TEST_LOCATION_NUMBER_OF_BELLS,
                'tenorWeight' => TestConstants::TEST_LOCATION_WEIGHT,
            ],
        ];

        $this->assertEquals(
            Response::STATUS_SUCCESS,
            $response->getStatus(),
            'Incorrect response status'
        );
        $this->assertEquals(
            $expectedData,
            $response->getData(),
            'Incorrect response data'
        );
    }
}
