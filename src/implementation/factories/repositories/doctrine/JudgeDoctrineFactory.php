<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\repositories\doctrine;


use DrlArchive\core\interfaces\factories\repositories\JudgeRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\JudgeRepositoryInterface;
use DrlArchive\implementation\repositories\doctrine\DoctrineDatabase;
use DrlArchive\implementation\repositories\doctrine\JudgeDoctrine;

class JudgeDoctrineFactory implements JudgeRepositoryFactoryInterface
{

    public function create(): JudgeRepositoryInterface
    {
        return new JudgeDoctrine(DoctrineDatabase::createConnection());
    }
}
