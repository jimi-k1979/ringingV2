<?php

declare(strict_types=1);

require_once __DIR__ . '/init.php';

use Twig\Loader\FilesystemLoader;


$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Twig\Environment(
    $loader,
    [
    ]
);

$twig->display('index.twig');

