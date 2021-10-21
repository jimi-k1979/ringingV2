<?php

declare(strict_types=1);

namespace DrlArchive\core\interactors\pages\judgePage;

use DrlArchive\core\classes\Request;

class JudgePageRequest extends Request
{
    public const JUDGE_ID = 'judgeId';

    protected array $schema = [
        self::JUDGE_ID => [
            parent::OPTION_TYPE => parent::FIELD_TYPE_INT,
            parent::OPTION_REQUIRED => true,
            parent::OPTION_ALLOW_NULL => false,
            parent::OPTION_DEFAULT => 0,
        ],
    ];

    public function getJudgeId(): int
    {
        return $this->data[self::JUDGE_ID];
    }

    public function setJudgeId(int $input): void
    {
        $this->updateModel(self::JUDGE_ID, $input);
    }

}
