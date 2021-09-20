<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\ringerPage;

use DrlArchive\core\classes\Request;

class RingerPageRequest extends Request
{
    public const RINGER_ID = 'ringerId';

    protected array $schema = [
        self::RINGER_ID => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
            parent::OPTION_DEFAULT => 0,
        ],
    ];

    public function getRingerId(): int
    {
        return $this->data[self::RINGER_ID];
    }

    public function setRingerId(int $input): void
    {
        $this->updateModel(self::RINGER_ID, $input);
    }

}
