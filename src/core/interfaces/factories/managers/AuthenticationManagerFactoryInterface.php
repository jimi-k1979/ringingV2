<?php

declare(strict_types=1);

namespace DrlArchive\core\interfaces\factories\managers;


use DrlArchive\core\interfaces\managers\AuthenticationManagerInterface;

interface AuthenticationManagerFactoryInterface
{

    public function create(): AuthenticationManagerInterface;
}
