<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\indexPage;


use DrlArchive\core\classes\Request;
use DrlArchive\core\classes\Response;

class IndexPageRequest extends Request
{
    public const PREVIOUS_STATUS = 'previousStatus';

    protected array $schema = [
        self::PREVIOUS_STATUS => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => false,
            parent::OPTION_ALLOW_NULL => true,
            parent::OPTION_DEFAULT => Response::STATUS_SUCCESS,
        ],
    ];

    public function getPreviousStatus(): int
    {
        return $this->data[self::PREVIOUS_STATUS];
    }

    public function setPreviousStatus(int $input): void
    {
        $this->updateModel(self::PREVIOUS_STATUS, $input);
    }


}
