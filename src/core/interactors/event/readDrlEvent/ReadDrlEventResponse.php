<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\event\readDrlEvent;


use DrlArchive\core\classes\Response;

class ReadDrlEventResponse extends Response
{
    public const DATA_EVENT = 'event';
    public const DATA_EVENT_RESULTS = 'results';
    public const DATA_EVENT_RESULTS_POSITION = 'position';
}
