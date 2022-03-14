<?php

namespace DrlArchive\mocks;

use DrlArchive\core\interfaces\repositories\PagesRepositoryInterface;
use DrlArchive\traits\CreateMockStatisticsRecordTrait;

class PageRepositoryDummy implements PagesRepositoryInterface
{
    use CreateMockStatisticsRecordTrait;

    public function fetchRecordsPageList(): array
    {
        return [$this->createMockStatisticsRecord()];
    }
}
