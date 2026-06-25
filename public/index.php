<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Router.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Models/Book.php';
require_once __DIR__ . '/../app/Models/Student.php';
require_once __DIR__ . '/../app/Models/Loan.php';

use App\Core\Router;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Student;

header('Content-Type: application/json; charset=utf-8');

$router = new Router();

function jsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

function requestBody(): array
{
    $rawBody = file_get_contents('php://input');

    if ($rawBody === false || trim($rawBody) === '') {
        return [];
    }

    $body = json_decode($rawBody, true);

    if (!is_array($body)) {
        jsonResponse(['message' => 'JSONの形式が正しくありません'], 400);
        exit;
    }

    return $body;
}

function requireFields(array $body, array $fields): void
{
    foreach ($fields as $field) {
        if (!array_key_exists($field, $body) || $body[$field] === '') {
            jsonResponse(['message' => "{$field} は必須です"], 422);
            exit;
        }
    }
}

$router->get('/api/health', function (): void {
    jsonResponse([
        'status' => 'ok',
        'message' => 'API is running',
    ]);
});

$router->get('/api/books', function (): void {
    jsonResponse([
        'data' => Book::all([
            'status' => $_GET['status'] ?? '',
            'keyword' => $_GET['keyword'] ?? '',
        ]),
    ]);
});

$router->get('/api/books/{id}', function (array $params): void {
    $book = Book::find((int) $params['id']);

    if ($book === null) {
        jsonResponse(['message' => '本が見つかりません'], 404);
        return;
    }

    jsonResponse(['data' => $book]);
});

$router->post('/api/books', function (): void {
    $body = requestBody();
    requireFields($body, ['title', 'barcode', 'location']);

    jsonResponse([
        'message' => '本を登録しました',
        'data' => Book::create($body),
    ], 201);
});

$router->put('/api/books/{id}', function (array $params): void {
    $body = requestBody();
    requireFields($body, ['title', 'barcode', 'location', 'status']);

    $book = Book::update((int) $params['id'], $body);

    if ($book === null) {
        jsonResponse(['message' => '本が見つかりません'], 404);
        return;
    }

    jsonResponse([
        'message' => '本を更新しました',
        'data' => $book,
    ]);
});

$router->delete('/api/books/{id}', function (array $params): void {
    if (!Book::delete((int) $params['id'])) {
        jsonResponse(['message' => '本が見つかりません'], 404);
        return;
    }

    jsonResponse(['message' => '本を削除しました']);
});

$router->get('/api/students', function (): void {
    jsonResponse([
        'data' => Student::all(),
    ]);
});

$router->get('/api/students/{id}', function (array $params): void {
    $student = Student::find((int) $params['id']);

    if ($student === null) {
        jsonResponse(['message' => '学生が見つかりません'], 404);
        return;
    }

    jsonResponse(['data' => $student]);
});

$router->post('/api/students', function (): void {
    $body = requestBody();
    requireFields($body, ['name', 'grade', 'student_id', 'email']);

    jsonResponse([
        'message' => '学生を登録しました',
        'data' => Student::create($body),
    ], 201);
});

$router->get('/api/loans', function (): void {
    jsonResponse([
        'data' => Loan::all(),
    ]);
});

$router->get('/api/loans/overdue', function (): void {
    jsonResponse([
        'data' => Loan::overdue(),
    ]);
});

$router->post('/api/loans', function (): void {
    $body = requestBody();
    requireFields($body, ['student_id', 'book_id']);

    $result = Loan::borrow((int) $body['student_id'], (int) $body['book_id']);

    if (!$result['success']) {
        jsonResponse([
            'message' => $result['message'],
            'error' => $result['error'] ?? null,
        ], $result['status']);
        return;
    }

    jsonResponse([
        'message' => '本を貸し出しました',
        'data' => $result['loan'],
    ], 201);
});

$router->post('/api/loans/{id}/return', function (array $params): void {
    $result = Loan::returnBook((int) $params['id']);

    if (!$result['success']) {
        jsonResponse([
            'message' => $result['message'],
            'error' => $result['error'] ?? null,
        ], $result['status']);
        return;
    }

    jsonResponse([
        'message' => '本を返却しました',
        'data' => $result['loan'],
    ]);
});

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);
