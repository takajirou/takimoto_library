<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Book
{
    public static function all(array $filters = []): array
    {
        $conditions = [];
        $params = [];

        if (($filters['status'] ?? '') !== '') {
            $conditions[] = 'status = :status';
            $params['status'] = $filters['status'];
        }

        if (($filters['keyword'] ?? '') !== '') {
            $conditions[] = '(title LIKE :keyword OR author LIKE :keyword OR isbn LIKE :keyword OR barcode LIKE :keyword)';
            $params['keyword'] = '%' . $filters['keyword'] . '%';
        }

        $sql = 'SELECT * FROM books';

        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY created_at DESC';

        $statement = Database::connect()->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $statement = Database::connect()->prepare('SELECT * FROM books WHERE id = :id');
        $statement->execute(['id' => $id]);
        $book = $statement->fetch();

        return $book === false ? null : $book;
    }

    public static function create(array $data): array
    {
        $pdo = Database::connect();

        $statement = $pdo->prepare(
            'INSERT INTO books (title, author, isbn, barcode, location, status)
             VALUES (:title, :author, :isbn, :barcode, :location, :status)'
        );

        $statement->execute([
            'title' => $data['title'],
            'author' => $data['author'] ?? null,
            'isbn' => $data['isbn'] ?? null,
            'barcode' => $data['barcode'],
            'location' => $data['location'],
            'status' => $data['status'] ?? 'available',
        ]);

        return self::find((int) $pdo->lastInsertId());
    }

    public static function update(int $id, array $data): ?array
    {
        if (self::find($id) === null) {
            return null;
        }

        $statement = Database::connect()->prepare(
            'UPDATE books
             SET title = :title, author = :author, isbn = :isbn, barcode = :barcode, location = :location, status = :status
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $id,
            'title' => $data['title'],
            'author' => $data['author'] ?? null,
            'isbn' => $data['isbn'] ?? null,
            'barcode' => $data['barcode'],
            'location' => $data['location'],
            'status' => $data['status'],
        ]);

        return self::find($id);
    }

    public static function delete(int $id): bool
    {
        if (self::find($id) === null) {
            return false;
        }

        $statement = Database::connect()->prepare('DELETE FROM books WHERE id = :id');
        $statement->execute(['id' => $id]);

        return true;
    }

    public static function updateStatus(int $id, string $status): void
    {
        $statement = Database::connect()->prepare('UPDATE books SET status = :status WHERE id = :id');
        $statement->execute([
            'id' => $id,
            'status' => $status,
        ]);
    }
}
