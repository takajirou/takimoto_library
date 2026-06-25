<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use Throwable;

class Loan
{
    public static function all(): array
    {
        $sql = <<<SQL
            SELECT
                loans.*,
                students.name AS student_name,
                students.student_id AS student_number,
                books.title AS book_title,
                books.barcode AS book_barcode
            FROM loans
            INNER JOIN students ON students.id = loans.student_id
            INNER JOIN books ON books.id = loans.book_id
            ORDER BY loans.borrowed_at DESC
        SQL;

        return Database::connect()->query($sql)->fetchAll();
    }

    public static function overdue(): array
    {
        $sql = <<<SQL
            SELECT
                loans.*,
                students.name AS student_name,
                students.student_id AS student_number,
                books.title AS book_title,
                books.barcode AS book_barcode
            FROM loans
            INNER JOIN students ON students.id = loans.student_id
            INNER JOIN books ON books.id = loans.book_id
            WHERE loans.status = 'borrowed'
              AND loans.due_date < CURDATE()
            ORDER BY loans.due_date ASC
        SQL;

        return Database::connect()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $statement = Database::connect()->prepare('SELECT * FROM loans WHERE id = :id');
        $statement->execute(['id' => $id]);
        $loan = $statement->fetch();

        return $loan === false ? null : $loan;
    }

    public static function borrow(int $studentId, int $bookId): array
    {
        $pdo = Database::connect();

        try {
            $pdo->beginTransaction();

            $student = Student::find($studentId);
            $book = Book::find($bookId);

            if ($student === null || $book === null) {
                $pdo->rollBack();

                return [
                    'success' => false,
                    'status' => 404,
                    'message' => '学生または本が見つかりません',
                ];
            }

            if ($book['status'] !== 'available') {
                $pdo->rollBack();

                return [
                    'success' => false,
                    'status' => 409,
                    'message' => 'この本は現在貸し出しできません',
                ];
            }

            if (Student::borrowedBookCount($studentId) >= 3) {
                $pdo->rollBack();

                return [
                    'success' => false,
                    'status' => 409,
                    'message' => '同時に借りられる本は3冊までです',
                ];
            }

            $statement = $pdo->prepare(
                "INSERT INTO loans (student_id, book_id, borrowed_at, due_date, status)
                 VALUES (:student_id, :book_id, NOW(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'borrowed')"
            );
            $statement->execute([
                'student_id' => $studentId,
                'book_id' => $bookId,
            ]);

            $loanId = (int) $pdo->lastInsertId();
            Book::updateStatus($bookId, 'borrowed');

            $pdo->commit();

            return [
                'success' => true,
                'loan' => self::find($loanId),
            ];
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return [
                'success' => false,
                'status' => 500,
                'message' => '貸し出し処理に失敗しました',
                'error' => $exception->getMessage(),
            ];
        }
    }

    public static function returnBook(int $loanId): array
    {
        $pdo = Database::connect();

        try {
            $pdo->beginTransaction();

            $loan = self::find($loanId);

            if ($loan === null) {
                $pdo->rollBack();

                return [
                    'success' => false,
                    'status' => 404,
                    'message' => '貸し出し情報が見つかりません',
                ];
            }

            if ($loan['status'] === 'returned') {
                $pdo->rollBack();

                return [
                    'success' => false,
                    'status' => 409,
                    'message' => 'この本はすでに返却済みです',
                ];
            }

            $statement = $pdo->prepare(
                "UPDATE loans SET returned_at = NOW(), status = 'returned' WHERE id = :id"
            );
            $statement->execute(['id' => $loanId]);

            Book::updateStatus((int) $loan['book_id'], 'available');

            $pdo->commit();

            return [
                'success' => true,
                'loan' => self::find($loanId),
            ];
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return [
                'success' => false,
                'status' => 500,
                'message' => '返却処理に失敗しました',
                'error' => $exception->getMessage(),
            ];
        }
    }
}
