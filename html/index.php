<?php

declare(strict_types=1);

require_once __DIR__ . '/init.php';

try {
    $twig->display('index.twig');
} catch (Throwable $e) {
    include __DIR__ . '/templates/failed.html';
}
