<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\factories\repositories;


use DrlArchive\core\interfaces\repositories\DeaneryRepositoryInterface;

interface DeaneryRepositoryFactoryInterface
{
    public function create(): DeaneryRepositoryInterface;
}