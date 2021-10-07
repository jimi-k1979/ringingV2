<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\location\fetchLocationByName;


use DrlArchive\core\classes\Response;

class FetchLocationByNameResponse extends Response
{
    public const DATA_LOCATION_ID = 'locationId';
    public const DATA_NAME = 'name';
    public const DATA_DEDICATION = 'dedication';
    public const DATA_TENOR_WEIGHT = 'tenorWeight';
    public const DATA_NUMBER_OF_BELLS = 'numberOfBells';
    public const DATA_DEANERY = 'deanery';
    public const DATA_REGION = 'region';
}
