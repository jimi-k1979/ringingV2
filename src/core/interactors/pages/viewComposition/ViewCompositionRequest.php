<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\viewComposition;

use DrlArchive\core\classes\Request;

class ViewCompositionRequest extends Request
{
    public const COMPOSITION_ID = 'compositionId';
    public const UP_CHANGES = 'upChanges';

    protected array $schema = [
        self::COMPOSITION_ID => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
            parent::OPTION_DEFAULT => 0,
        ],
        self::UP_CHANGES => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_BOOL,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
            parent::OPTION_DEFAULT => true,
        ],
    ];

    public function getCompositionId(): int
    {
        return $this->data[self::COMPOSITION_ID];
    }

    public function setCompositionId(int $input): void
    {
        $this->updateModel(self::COMPOSITION_ID, $input);
    }

    public function isUpChanges(): bool
    {
        return $this->data[self::UP_CHANGES];
    }

    public function setUpChanges(bool $input): void
    {
        $this->updateModel(self::UP_CHANGES, $input);
    }


}
