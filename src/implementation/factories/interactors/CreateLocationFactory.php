<?php
declare(strict_types=1);

namespace DrlArchive\implementation\factories\interactors;


use DrlArchive\core\classes\Request;
use DrlArchive\core\factories\interactors\InteractorFactoryInterface;
use DrlArchive\core\interfaces\boundaries\InteractorInterface;
use DrlArchive\core\interfaces\boundaries\PresenterInterface;

class CreateLocationFactory implements InteractorFactoryInterface
{

    public function create(PresenterInterface $presenter, ?Request $request = null): InteractorInterface
    {
        // TODO: Implement create() method.
    }
}