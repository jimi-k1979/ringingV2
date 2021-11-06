<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\compositionPage;

use DrlArchive\core\classes\Response;

class CompositionPageResponse extends Response
{
    public const DATA_COMPOSITION_ID = 'compositionId';
    public const DATA_COMPOSITION = 'composition';
    public const DATA_BELLS = 'numberOfBells';
    public const DATA_TENOR = 'tenorTurnedIn';
    public const DATA_DESCRIPTION = 'description';
    public const DATA_LENGTH = 'length';
}
