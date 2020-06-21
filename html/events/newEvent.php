<?php

declare(strict_types=1);


require_once __DIR__ . '/../init.php';

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
