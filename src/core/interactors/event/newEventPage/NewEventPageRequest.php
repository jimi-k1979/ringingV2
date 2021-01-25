<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\newEventPage;


use DrlArchive\core\classes\Request;

class NewEventPageRequest extends Request
{
    public const YEAR = 'year';
    public const COMPETITION_ID = 'competitionId';
    public const LOCATION_ID = 'locationId';
    public const USUAL_LOCATION_ID = 'usualLocationId';
    public const RESULTS_ARRAY = 'results';

    protected array $schema = [
        self::YEAR => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_STRING,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
        self::COMPETITION_ID => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
        self::LOCATION_ID => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
        ],
        self::USUAL_LOCATION_ID => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => false,
            parent::OPTION_ALLOW_NULL => true,
            parent::OPTION_DEFAULT => null,
        ],
        self::RESULTS_ARRAY => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_ARRAY,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
            parent::OPTION_DEFAULT => [],
        ],
    ];

    public function getYear(): string
    {
        return $this->data[self::YEAR];
    }

    public function setYear(string $input): void
    {
        $this->updateModel(self::YEAR, $input);
    }

    public function getCompetitionId(): int
    {
        return $this->data[self::COMPETITION_ID];
    }

    public function setCompetitionId(int $input): void
    {
        $this->updateModel(self::COMPETITION_ID, $input);
    }

    public function getLocationId(): int
    {
        return $this->data[self::LOCATION_ID];
    }

    public function setLocationId(int $input): void
    {
        $this->updateModel(self::LOCATION_ID, $input);
    }

    public function getUsualLocation(): ?int
    {
        return $this->data[self::USUAL_LOCATION_ID];
    }

    public function setUsualLocation(?int $input): void
    {
        $this->updateModel(self::USUAL_LOCATION_ID, $input);
    }

    public function getResults(): array
    {
        return $this->data[self::RESULTS_ARRAY];
    }

    public function setResults(array $input): void
    {
        $this->updateModel(self::RESULTS_ARRAY, $input);
    }

    public function addResultsRow(int $position, float $faults, string $teamName, ?int $pealNumber = null): void
    {
        $this->data[self::RESULTS_ARRAY][] = [
            NewEventPage::POSITION => $position,
            NewEventPage::FAULTS => $faults,
            NewEventPage::TEAM_NAME => $teamName,
            NewEventPage::PEAL_NUMBER => $pealNumber
        ];
        $this->updateModel(
            self::RESULTS_ARRAY,
            $this->data[self::RESULTS_ARRAY]
        );
    }
}
