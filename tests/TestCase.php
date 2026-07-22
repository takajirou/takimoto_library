<?php

namespace Tests;

use App\Core\Database;
use App\Models\Book;
use App\Models\Student;
use PHPUnit\Framework\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    private int $sequence = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $_ENV['DB_DATABASE'] = 'takimoto_library_test';
        $_SERVER['DB_DATABASE'] = 'takimoto_library_test';

        $pdo = Database::connect();

        if ($pdo->query('SELECT DATABASE()')->fetchColumn() !== 'takimoto_library_test') {
            throw new RuntimeException('テスト用DBに接続できていないため、テストを中断しました。');
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $pdo->exec('TRUNCATE TABLE loans');
        $pdo->exec('TRUNCATE TABLE books');
        $pdo->exec('TRUNCATE TABLE students');
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    protected function createStudent(array $overrides = []): array
    {
        $number = ++$this->sequence;

        return Student::create(array_replace([
            'name' => "テスト学生{$number}",
            'grade' => 1,
            'student_id' => 100000 + $number,
            'email' => "student{$number}@example.test",
        ], $overrides));
    }

    protected function createBook(array $overrides = []): array
    {
        $number = ++$this->sequence;

        return Book::create(array_replace([
            'title' => "テスト本{$number}",
            'barcode' => "TEST-{$number}",
            'location' => 'A-1',
        ], $overrides));
    }
}
