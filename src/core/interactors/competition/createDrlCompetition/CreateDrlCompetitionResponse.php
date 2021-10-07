<?php
declare(strict_types=1);

namespace DrlArchive\core\interactors\competition\createDrlCompetition;


use DrlArchive\core\classes\Response;

class CreateDrlCompetitionResponse extends Response
{
    public const DATA_ID = 'id';
    public const DATA_NAME = 'name';
    public const DATA_SINGLE_TOWER = 'singleTower';
}
