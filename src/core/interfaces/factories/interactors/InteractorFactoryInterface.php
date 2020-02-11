<?php
declare(strict_types=1);

namespace DrlArchive\core\factories\interactors;


use DrlArchive\core\classes\Request;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;


interface InteractorFactoryInterface
{
    public function create(
        PresenterInterface $presenter,
        ?Request $request = null
    ): InteractorInterface;
}