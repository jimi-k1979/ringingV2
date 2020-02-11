<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\factories\repositories;


use DrlArchive\core\interfaces\repositories\TeamRepositoryInterface;

interface TeamRepositoryFactoryInterface
{
    public function create(): TeamRepositoryInterface;
}