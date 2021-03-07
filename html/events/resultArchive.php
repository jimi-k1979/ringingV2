<?php

/**
 * @var Environment $twig
 */
declare(strict_types=1);

require_once(__DIR__ . '/../init.php');

use Twig\Environment;

try {
    $twig->display('events/eventSearch.twig');
} catch (Throwable $e) {
    include __DIR__ . '/../templates/failed.html';
}
