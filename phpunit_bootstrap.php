<?php
declare(strict_types=1);
putenv('ENVIRONMENT=testing');


$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/html');
//require_once __DIR__ . '/../../Connections/class.connection.php';
//require_once __DIR__ . '/../../Connections/class.connection.replica.php';

//require_once __DIR__ . '/../../html/classes/apiHandler/ApiHandler.php';
//require_once __DIR__ . '/../../html/classes/class.model.php';
//foreach (glob(__DIR__ . '/../../html/classes/*.php') as $file) {
//    require_once $file;
//}

foreach (glob(__DIR__ . '/src/**/**/*.php') as $file) {
    require_once $file;
}

//foreach (glob(__DIR__ . '/src/core/interactors/**/*.php') as $file) {
//    require_once $file;
//}

/**
 * Add mocks
 */
//foreach (glob(__DIR__ . '/test/mocks/traits/*.php') as $file) {
//    require_once $file;
//}
foreach (glob(__DIR__ . '/test/mocks/*.php') as $file) {
    require_once $file;
}
//foreach (glob(__DIR__ . '/mocks/external/*.php') as $file) {
//    require_once $file;
//}