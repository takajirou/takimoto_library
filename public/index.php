<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Router.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Controllers/Controller.php';
require_once __DIR__ . '/../app/Controllers/HealthController.php';
require_once __DIR__ . '/../app/Controllers/BookController.php';
require_once __DIR__ . '/../app/Controllers/StudentController.php';
require_once __DIR__ . '/../app/Controllers/LoanController.php';
require_once __DIR__ . '/../app/Models/Book.php';
require_once __DIR__ . '/../app/Models/Student.php';
require_once __DIR__ . '/../app/Models/Loan.php';

use App\Controllers\BookController;
use App\Controllers\HealthController;
use App\Controllers\LoanController;
use App\Controllers\StudentController;
use App\Core\Router;

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/') {
    require __DIR__ . '/home.php';
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$router = new Router();

$healthController = new HealthController();
$bookController = new BookController();
$studentController = new StudentController();
$loanController = new LoanController();

$router->get('/api/health', [$healthController, 'index']);

$router->get('/api/books', [$bookController, 'index']);
$router->get('/api/books/{id}', [$bookController, 'show']);
$router->post('/api/books', [$bookController, 'store']);
$router->put('/api/books/{id}', [$bookController, 'update']);
$router->delete('/api/books/{id}', [$bookController, 'destroy']);

$router->get('/api/students', [$studentController, 'index']);
$router->get('/api/students/{id}', [$studentController, 'show']);
$router->post('/api/students', [$studentController, 'store']);

$router->get('/api/loans', [$loanController, 'index']);
$router->get('/api/loans/overdue', [$loanController, 'overdue']);
$router->post('/api/loans', [$loanController, 'store']);
$router->post('/api/loans/{id}/return', [$loanController, 'returnBook']);

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);
