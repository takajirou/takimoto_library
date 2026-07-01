<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Book;

class BookController extends Controller
{
    public function index(array $params = []): void
    {
        $this->jsonResponse([
            'data' => Book::all([
                'status' => $_GET['status'] ?? '',
                'keyword' => $_GET['keyword'] ?? '',
            ]),
        ]);
    }

    public function show(array $params): void
    {
        $book = Book::find((int) $params['id']);

        if ($book === null) {
            $this->jsonResponse(['message' => '本が見つかりません'], 404);
            return;
        }

        $this->jsonResponse(['data' => $book]);
    }

    public function store(array $params = []): void
    {
        $body = $this->requestBody();
        $this->requireFields($body, ['title', 'barcode', 'location']);

        $this->jsonResponse([
            'message' => '本を登録しました',
            'data' => Book::create($body),
        ], 201);
    }

    public function update(array $params): void
    {
        $body = $this->requestBody();
        $this->requireFields($body, ['title', 'barcode', 'location', 'status']);

        $book = Book::update((int) $params['id'], $body);

        if ($book === null) {
            $this->jsonResponse(['message' => '本が見つかりません'], 404);
            return;
        }

        $this->jsonResponse([
            'message' => '本を更新しました',
            'data' => $book,
        ]);
    }

    public function destroy(array $params): void
    {
        if (!Book::delete((int) $params['id'])) {
            $this->jsonResponse(['message' => '本が見つかりません'], 404);
            return;
        }

        $this->jsonResponse(['message' => '本を削除しました']);
    }
}
