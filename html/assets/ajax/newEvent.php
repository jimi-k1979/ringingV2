<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../vendor/autoload.php';

header('Content-Type: application/json');

try {
    switch ($_POST['action']) {
        case 'getCompetitionDetails':

            break;

        default:
            echo json_encode(
                [
                    [
                        'name' => 'Nothing found',
                        'id' => 0,
                        'text' => 'Not found'
                    ],
                ]
            );
            break;
    }
} catch (Exception $e) {
    echo json_encode(
        [
            [
                'name' => 'Nothing found',
                'id' => 0,
                'text' => 'Not found'
            ],
        ]
    );
}