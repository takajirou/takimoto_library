<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Router.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Controllers/Controller.php';
require_once __DIR__ . '/../app/Controllers/HealthController.php';
require_once __DIR__ . '/../app/Controllers/BookController.php';
require_once __DIR__ . '/../app/Controllers/StudentController.php';
require_once __DIR__ . '/../app/Controllers/LoanController.php';
require_once __DIR__ . '/../app/Controllers/PageController.php';
require_once __DIR__ . '/../app/Models/Book.php';
require_once __DIR__ . '/../app/Models/Student.php';
require_once __DIR__ . '/../app/Models/Loan.php';

use App\Controllers\BookController;
use App\Controllers\HealthController;
use App\Controllers\LoanController;
use App\Controllers\PageController;
use App\Controllers\StudentController;
use App\Core\Router;

$router = new Router();

$pageController = new PageController();
$healthController = new HealthController();
$bookController = new BookController();
$studentController = new StudentController();
$loanController = new LoanController();

$router->get('/', [$pageController, 'home']);
$router->get('/books', [$pageController, 'books']);
$router->get('/borrow', [$pageController, 'borrow']);
$router->post('/borrow', [$pageController, 'borrow']);
$router->get('/return', [$pageController, 'return']);
$router->post('/return', [$pageController, 'return']);
$router->get('/students', [$pageController, 'students']);
$router->post('/students', [$pageController, 'students']);
$router->get('/loans', [$pageController, 'loans']);
$router->get('/admin-books', [$pageController, 'adminBooks']);
$router->post('/admin-books', [$pageController, 'adminBooks']);

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
