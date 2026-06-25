<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Student
{
    public static function all(): array
    {
        $statement = Database::connect()->query('SELECT * FROM students ORDER BY created_at DESC');

        return $statement->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $statement = Database::connect()->prepare('SELECT * FROM students WHERE id = :id');
        $statement->execute(['id' => $id]);
        $student = $statement->fetch();

        return $student === false ? null : $student;
    }

    public static function create(array $data): array
    {
        $pdo = Database::connect();

        $statement = $pdo->prepare(
            'INSERT INTO students (name, grade, student_id, email)
             VALUES (:name, :grade, :student_id, :email)'
        );

        $statement->execute([
            'name' => $data['name'],
            'grade' => (int) $data['grade'],
            'student_id' => (int) $data['student_id'],
            'email' => $data['email'],
        ]);

        return self::find((int) $pdo->lastInsertId());
    }

    public static function borrowedBookCount(int $id): int
    {
        $statement = Database::connect()->prepare(
            "SELECT COUNT(*) FROM loans WHERE student_id = :student_id AND status = 'borrowed'"
        );
        $statement->execute(['student_id' => $id]);

        return (int) $statement->fetchColumn();
    }
}
