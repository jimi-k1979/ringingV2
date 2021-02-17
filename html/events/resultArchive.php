<?php

/**
 * @var Environment $twig
 */
declare(strict_types=1);

use Twig\Environment;

require_once __DIR__ . '/../init.php';

try {
    $twig->display('events/eventSearch.twig');
} catch (Throwable $e) {
    include __DIR__ . '/../templates/failed.html';
}
