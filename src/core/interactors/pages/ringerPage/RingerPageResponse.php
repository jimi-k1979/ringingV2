<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\ringerPage;

use DrlArchive\core\classes\Response;

class RingerPageResponse extends Response
{
    public const DATA_RINGER = 'ringer';
    public const DATA_RINGER_ID = 'id';
    public const DATA_RINGER_FIRST_NAME = 'firstName';
    public const DATA_RINGER_LAST_NAME = 'lastName';
    public const DATA_RINGER_NOTES = 'notes';
    public const DATA_RINGER_JUDGE_ID = 'judgeId';
    public const DATA_EVENTS = 'events';
    public const DATA_EVENT_ID = 'id';
    public const DATA_EVENT_YEAR = 'year';
    public const DATA_EVENT_EVENT = 'event';
    public const DATA_EVENT_BELL = 'bell';
    public const DATA_STATS = 'statistics';

}
