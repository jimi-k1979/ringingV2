<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment(
    $loader,
    [
    ]
);
