<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\judgePage;

use DrlArchive\core\classes\Response;

class JudgePageResponse extends Response
{
    public const DATA_JUDGE = 'judge';
    public const DATA_JUDGE_ID = 'id';
    public const DATA_JUDGE_FIRST_NAME = 'firstName';
    public const DATA_JUDGE_LAST_NAME = 'lastName';
    public const DATA_JUDGE_RINGER_ID = 'ringerId';
    public const DATA_EVENTS = 'events';
    public const DATA_EVENT_ID = 'eventId';
    public const DATA_EVENT_YEAR = 'year';
    public const DATA_EVENT_EVENT = 'event';
    public const DATA_STATS = 'statistics';
    public const DATA_STATS_NO_OF_EVENTS = 'numberOfEvents';

}
