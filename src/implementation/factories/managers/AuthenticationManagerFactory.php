<?php

declare(strict_types=1);

namespace DrlArchive\implementation\factories\managers;


use DrlArchive\core\interfaces\factories\managers\AuthenticationManagerFactoryInterface;
use DrlArchive\core\interfaces\managers\AuthenticationManagerInterface;
use DrlArchive\implementation\repositories\managers\AuthenticationManager;

class AuthenticationManagerFactory implements
    AuthenticationManagerFactoryInterface
{

    public function create(): AuthenticationManagerInterface
    {
        return AuthenticationManager::createManager();
    }
}
