<?php

namespace DrlArchive\core\interfaces\factories\repositories;

use DrlArchive\core\interfaces\repositories\PagesRepositoryInterface;

interface PageRepositoryFactoryInterface
{
    public function create(): PagesRepositoryInterface;

}
