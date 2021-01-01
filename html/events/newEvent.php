<?php

declare(strict_types=1);


use Twig\Environment;

require_once __DIR__ . '/../init.php';

/** @var Environment $twig */
try {
    echo $twig->render(
        'events/newEvent.twig',
        [
            'maxYear' => new DateTime(),
        ]
    );
} catch (Throwable $e) {
    include __DIR__ . '/../templates/failed.html';
}
