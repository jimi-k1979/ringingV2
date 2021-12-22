<?php

namespace DrlArchive\core\interactors\pages\teamPage;

use DrlArchive\core\interactors\Interactor;
use PHPUnit\Framework\TestCase;

class TeamPageTest extends TestCase
{
    public function testInstantiation(): void
    {
        $this->assertInstanceOf(
            Interactor::class,
            new TeamPage()
        );
    }

    public function testRequestDefaults(): void
    {
        $request = new TeamPageRequest();

        $this->assertEquals(
            0,
            $request->getTeamId(),
            'Invalid team id'
        );
        $this->assertTrue(
            $request->isShowStats(),
            'Invalid show stats'
        );
        $this->assertEquals(
            [
                'startYear' => 0,
                'endYear' => 0,
                'rangeSummary' => [
                    'firstYear' => true,
                    'mostRecentYear' => true,
                    'seasonCount' => false,
                    'eventCount' => true,
                    'eventsPerSeason' => false,
                    'rankingMean' => true,
                    'rankingMedian' => false,
                    'rankingMode' => false,
                    'rankingRange' => false,
                    'positionMean' => true,
                    'positionMedian' => false,
                    'positionMode' => false,
                    'positionRange' => false,
                    'faultTotal' => true,
                    'faultMean' => true,
                    'faultMedian' => true,
                    // 'faultMode' is not greatly informative
                    'faultRange' => false,
                    'faultDifferenceTotal' => true,
                    'faultDifferenceMean' => false,
                    'faultDifferenceMedian' => false,
                    // 'faultDifferenceMode' is not greatly informative
                    'faultDifferenceRange' => false,
                    'leaguePointTotal' => true,
                    'leaguePointMean' => true,
                    'leaguePointMedian' => false,
                    // 'leaguePointMode' is not greatly informative
                    'leaguePointRange' => false,
                    'noResultCount' => true,
                ],
                'seasonal' => [
                    'eventCount' => true,
                    'faultTotal' => true,
                    'faultMean' => true,
                    'faultRange' => false,
                    'positionMean' => true,
                    'positionMedian' => false,
                    'positionMode' => false,
                    'positionRange' => false,
                    'noResultCount' => true,
                    'leaguePointTotal' => true,
                    'leaguePointMean' => true, // aka ranking
                    'leaguePointMedian' => false,
                    'leaguePointMode' => false,
                    'leaguePointRange' => false,
                    'faultDifference' => true,
                ],
            ],
            $request->getStatsOptions(),
            'Default stats option array wrong'
        );
        $this->assertFalse(
            $request->isShowResults(),
            'Invalid show results'
        );
    }

}
