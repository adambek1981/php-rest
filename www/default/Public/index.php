<?php

require '../vendor/autoload.php';

try {
    App\Router::set('/', function () {
        return new App\Controllers\Root();
    });

    App\Router::set('/generate-data', function () {
        return new App\Controllers\GenerateData();
    });

    App\Router::set('/order/create', function () {
        return new App\Controllers\CreateOrder();
    });

    App\Router::set('/order/pay', function () {
        return new App\Controllers\PayOrder();
    });

    App\Router::run();
} catch (Throwable $exception) {
    $response = new App\Service\Response(500);
    $response->setException($exception);
    $response->sendToStream();
    exit();
}
