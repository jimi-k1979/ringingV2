<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\viewComposition;

use DrlArchive\core\classes\Response;

class ViewCompositionResponse extends Response
{
    public const DATA_COMPOSITION_NAME = 'compositionName';
    public const DATA_NUMBER_OF_CHANGES = 'numberOfChanges';
    public const DATA_CHANGES = 'changes';
    public const DATA_CHANGE_NUMBER = 'changeNumber';
    public const DATA_CHANGE_TEXT = 'changeText';
    public const DATA_DESCRIPTION = 'description';
}
