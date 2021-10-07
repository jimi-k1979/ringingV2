<?php
declare(strict_types=1);

namespace DrlArchive\core\interactors\location\createLocation;


use DrlArchive\core\classes\Response;

class CreateLocationResponse extends Response
{
    public const DATA_ID = 'id';
    public const DATA_LOCATION = 'location';
    public const DATA_DEANERY = 'deanery';
    public const DATA_DEDICATION = 'dedication';
    public const DATA_TENOR_WEIGHT = 'tenorWeight';
}
