<?php

namespace DrlArchive\implementation\factories\repositories\doctrine;

use DrlArchive\core\interfaces\factories\repositories\PageRepositoryFactoryInterface;
use DrlArchive\core\interfaces\repositories\PagesRepositoryInterface;
use DrlArchive\implementation\repositories\doctrine\DoctrineDatabase;
use DrlArchive\implementation\repositories\doctrine\PageDoctrine;

class PageDoctrineFactory implements PageRepositoryFactoryInterface
{

    public function create(): PagesRepositoryInterface
    {
        return new PageDoctrine(DoctrineDatabase::createConnection());
    }
}
