<?php

declare(strict_types=1);

require_once __DIR__ . '/../init.php';

use DrlArchive\core\classes\Response;
use DrlArchive\core\interactors\pages\teamPage\TeamPageRequest;
use DrlArchive\core\interactors\pages\teamPage\TeamPageResponse;
use DrlArchive\core\StatFieldNames;
use DrlArchive\Implementation;
use DrlArchive\implementation\factories\interactors\pages\TeamPageFactory;
use DrlArchive\implementation\presenters\AbstractTwigPagePresenter;


$presenter = new class extends AbstractTwigPagePresenter {
    private const GENERAL_TYPE = 'general';
    private const EVENT_TYPE = 'event';
    private const LEAGUE_TYPE = 'league';
    private const STAT_HEADER = 'statHeaders';
    private const VALUE = 'value';
    private const TYPE = 'type';
    private const TEXT_END = 'text-end';

    private array $stats;
    private array $statsOptions;
    private array $statsToShow = [
        StatFieldNames::RANGE_SUMMARY => [],
        StatFieldNames::SEASONAL => [],
    ];
    private array $results;
    private array $statHeaders = [];

    public function send(?Response $response = null): void
    {
        parent::send($response);
        $this->dataForTemplate[self::NAV][self::NAV_HIGHLIGHTED] =
            Implementation::NAV_HIGHLIGHT_STATS;

        if ($response->getStatus() === Response::STATUS_SUCCESS) {
            $this->stats =
                $response->getData()[TeamPageResponse::DATA_STATS];
            $this->statsOptions =
                $response->getData()[TeamPageResponse::DATA_STATS_OPTIONS];
            $this->results =
                $response->getData()[TeamPageResponse::DATA_RESULTS];

            if (!empty($this->stats)) {
                $this->filterForRequiredStats();
            }
            if (!empty($this->results)) {
                $this->processResults();
            }

            $this->dataForTemplate[self::TEAM] =
                $response->getData()[TeamPageResponse::DATA_TEAM];
            $this->dataForTemplate[self::STATS] = $this->statsToShow;
            $this->dataForTemplate[self::RESULTS] = $this->results;
            $this->dataForTemplate[self::STAT_HEADER] = $this->statHeaders;

            try {
                $this->twig->display(
                    'statistics/team.twig',
                    $this->dataForTemplate
                );
            } catch (Throwable $e) {
                echo $response->getMessage();
                die();
            }
        } else {
            echo $response->getMessage();
        }
    }

    private function filterForRequiredStats(): void
    {
        $this->statsToShow[StatFieldNames::RANGE_SUMMARY] =
            $this->processStats(
                $this->stats[StatFieldNames::RANGE_SUMMARY],
                $this->statsOptions[StatFieldNames::RANGE_SUMMARY]
            );
        foreach ($this->stats[StatFieldNames::SEASONAL] as $year => $data) {
            $this->statsToShow[StatFieldNames::SEASONAL][$year] =
                $this->processStats(
                    $data,
                    $this->statsOptions[StatFieldNames::SEASONAL]
                );
        }
    }

    private function processStats(array $data, array $options): array
    {
        $summary = [];
        foreach ($options as $stat => $option) {
            if ($option) {
                $fieldName = preg_split(
                    '/(?=[A-Z])/',
                    $stat
                );
                $fieldName = ucfirst(
                    implode(' ', $fieldName)
                );

                switch ($stat) {
                    case StatFieldNames::FIRST_YEAR:
                        $summary['Years'][self::VALUE] = $data[$stat];
                        if (
                            isset($data[StatFieldNames::MOST_RECENT_YEAR])
                            && $data[$stat] != $data[StatFieldNames::MOST_RECENT_YEAR]
                        ) {
                            $summary['Years'][self::VALUE] = $data[$stat] . ' - '
                                . $data[StatFieldNames::MOST_RECENT_YEAR];
                        }
                        $summary['Years'][self::TYPE] = self::GENERAL_TYPE;
                        $this->statHeaders[$fieldName] = self::GENERAL_TYPE;

                        break;

                    case StatFieldNames::MOST_RECENT_YEAR:
                        // do nothing
                        break;

                    case StatFieldNames::EVENTS_PER_SEASON:
                    case StatFieldNames::POSITION_MEAN:
                    case StatFieldNames::POSITION_MEDIAN:
                        $summary[$fieldName][self::VALUE] = number_format(
                            (float)$data[$stat],
                            2
                        );
                        $summary[$fieldName][self::TYPE] = self::GENERAL_TYPE
                            . ' ' . self::TEXT_END;
                        $this->statHeaders[$fieldName] = self::GENERAL_TYPE;
                        break;

                    case StatFieldNames::RANKING_MEAN:
                    case StatFieldNames::RANKING_MEDIAN:
                    case StatFieldNames::RANKING_RANGE:
                    case StatFieldNames::LEAGUE_POINT_MEAN:
                        $summary[$fieldName][self::VALUE] = number_format(
                            (float)$data[$stat],
                            2
                        );
                        $summary[$fieldName][self::TYPE] = self::LEAGUE_TYPE
                            . ' ' . self::TEXT_END;
                        $this->statHeaders[$fieldName] = self::LEAGUE_TYPE;
                        break;

                    case StatFieldNames::FAULT_TOTAL:
                    case StatFieldNames::FAULT_MEAN:
                    case StatFieldNames::FAULT_MEDIAN:
                    case StatFieldNames::FAULT_RANGE:
                    case StatFieldNames::FAULT_DIFFERENCE:
                    case StatFieldNames::FAULT_DIFFERENCE_TOTAL:
                    case StatFieldNames::FAULT_DIFFERENCE_MEAN:
                    case StatFieldNames::FAULT_DIFFERENCE_MEDIAN:
                    case StatFieldNames::FAULT_DIFFERENCE_RANGE:
                        $summary[$fieldName][self::VALUE] = number_format(
                            (float)$data[$stat],
                            2
                        );
                        $summary[$fieldName][self::TYPE] = self::EVENT_TYPE
                            . ' ' . self::TEXT_END;
                        $this->statHeaders[$fieldName] = self::EVENT_TYPE;
                        break;

                    case StatFieldNames::LEAGUE_POINT_MEDIAN:
                        if ($data[$stat] % 2 === 0) {
                            $summary[$fieldName][self::VALUE] = $data[$stat];
                        } else {
                            $summary[$fieldName][self::VALUE] = number_format(
                                (float)$data[$stat],
                                1
                            );
                        }
                        $summary[$fieldName][self::TYPE] = self::LEAGUE_TYPE
                            . ' ' . self::TEXT_END;
                        $this->statHeaders[$fieldName] = self::LEAGUE_TYPE;
                        break;

                    case StatFieldNames::EVENT_COUNT:
                    case StatFieldNames::NO_RESULT_COUNT:
                    case StatFieldNames::SEASON_COUNT:
                        $summary[$fieldName][self::VALUE] = $data[$stat];
                        $summary[$fieldName][self::TYPE] = self::GENERAL_TYPE
                            . ' ' . self::TEXT_END;
                        $this->statHeaders[$fieldName] = self::GENERAL_TYPE;
                        break;

                    default:
                        $summary[$fieldName][self::VALUE] = $data[$stat];
                        $summary[$fieldName][self::TYPE] = self::LEAGUE_TYPE
                            . ' ' . self::TEXT_END;
                        $this->statHeaders[$fieldName] = self::LEAGUE_TYPE;
                }
            }
        }
        return $summary;
    }

    private function processResults(): void
    {
        foreach ($this->results as $index => $result) {
            if (isset($result[StatFieldNames::FAULT_DIFFERENCE])) {
                $this->results[$index][StatFieldNames::FAULT_DIFFERENCE] =
                    number_format(
                        (float)$result[StatFieldNames::FAULT_DIFFERENCE],
                        2
                    );
            }
        }
    }
};

$request = new TeamPageRequest();
$request->setTeamId((int)$_GET['id']);
$request->setShowStats(true);
$request->setShowResults(true);

if (isset($_GET['statsOptions'])) {
    // decode and apply
}
if (isset($_GET['startYear'])) {
    $request->setStartYear((int)$_GET['startYear']);
}
if (isset($_GET['endYear'])) {
    $request->setEndYear((int)$_GET['endYear']);
}

$useCase = (new TeamPageFactory())->create(
    $presenter,
    $request
);
$useCase->execute();
