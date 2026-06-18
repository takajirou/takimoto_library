<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Router.php';

use App\Core\Router;

header('Content-Type: application/json; charset=utf-8');

$router = new Router();

$router->get('/api/health', function () {
    echo json_encode([
        'status' => 'ok',
        'message' => 'API is running',
    ], JSON_UNESCAPED_UNICODE);
});

$router->get('/api/books', function () {
    echo json_encode([
        'message' => '本一覧APIです',
    ], JSON_UNESCAPED_UNICODE);
});

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);