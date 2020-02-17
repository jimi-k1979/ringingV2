<?php
declare(strict_types=1);

namespace DrlArchive\core\interfaces\repositories;


use DrlArchive\core\entities\DeaneryEntity;

interface DeaneryRepositoryInterface
{
    public function selectDeanery(int $id): DeaneryEntity;

    public function getDeaneryByName(string $name): DeaneryEntity;
}