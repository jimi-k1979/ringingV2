<?php

namespace DrlArchive\core\entities;

use DrlArchive\core\interfaces\repositories\Repository;

class RecordRequestOptionsEntity
{
    public const ALL_RECORDS = -1;
    public const ALL_YEARS = -1;
    public const DEFAULT_MINIMUM_ENTRY = 5;
    public const ALL_TIME_RECORD = true;
    public const SEASONAL_RECORD = false;

    private int $numberOfRecords = self::ALL_RECORDS;
    private string $orderBy = Repository::ORDER_BY_ASC;
    private int $year = self::ALL_YEARS;
    private int $numberOfEventsFilter = self::DEFAULT_MINIMUM_ENTRY;
    private bool $allTimeRecord = self::ALL_TIME_RECORD;

    /**
     * @return int
     */
    public function getNumberOfRecords(): int
    {
        return $this->numberOfRecords;
    }

    /**
     * @param int $numberOfRecords
     */
    public function setNumberOfRecords(int $numberOfRecords): void
    {
        $this->numberOfRecords = $numberOfRecords;
    }

    /**
     * @return string
     */
    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    /**
     * @param string $orderBy
     */
    public function setOrderBy(string $orderBy): void
    {
        $this->orderBy = $orderBy;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    /**
     * @return int
     */
    public function getNumberOfEventsFilter(): int
    {
        return $this->numberOfEventsFilter;
    }

    /**
     * @param int $numberOfEventsFilter
     */
    public function setNumberOfEventsFilter(int $numberOfEventsFilter): void
    {
        $this->numberOfEventsFilter = $numberOfEventsFilter;
    }

    /**
     * @return bool
     */
    public function isAllTimeRecord(): bool
    {
        return $this->allTimeRecord;
    }

    public function setAllTimeRecord(): void
    {
        $this->allTimeRecord = self::ALL_TIME_RECORD;
    }

    public function setSeasonalRecord(): void
    {
        $this->allTimeRecord = self::SEASONAL_RECORD;
    }

}
