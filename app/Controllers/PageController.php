<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Student;
use Throwable;

class PageController extends Controller
{
    public function home(array $params = []): void
    {
        $this->render('home');
    }

    public function books(array $params = []): void
    {
        $books = Book::all([
            'status' => $_GET['status'] ?? '',
            'keyword' => $_GET['keyword'] ?? '',
        ]);

        $this->render('books', ['books' => $books]);
    }

    public function borrow(array $params = []): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $studentId = (int) $this->requiredValue($_POST, 'student_id');
            $bookId = (int) $this->requiredValue($_POST, 'book_id');

            if ($studentId <= 0 || $bookId <= 0) {
                $this->redirectTo('/borrow', ['error' => '学生と本を選択してください']);
            }

            $result = Loan::borrow($studentId, $bookId);

            if (!$result['success']) {
                $this->redirectTo('/borrow', ['error' => $result['message']]);
            }

            $this->redirectTo('/borrow', ['message' => '本を貸し出しました']);
        }

        $this->render('borrow', [
            'students' => Student::all(),
            'books' => Book::all(['status' => 'available']),
        ]);
    }

    public function return(array $params = []): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loanId = (int) $this->requiredValue($_POST, 'loan_id');

            if ($loanId <= 0) {
                $this->redirectTo('/return', ['error' => '貸し出し情報を選択してください']);
            }

            $result = Loan::returnBook($loanId);

            if (!$result['success']) {
                $this->redirectTo('/return', ['error' => $result['message']]);
            }

            $this->redirectTo('/return', ['message' => '本を返却しました']);
        }

        $loans = array_filter(
            Loan::all(),
            fn (array $loan): bool => $loan['status'] === 'borrowed'
        );

        $this->render('return', ['loans' => $loans]);
    }

    public function students(array $params = []): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $this->requiredValue($_POST, 'name');
            $grade = $this->requiredValue($_POST, 'grade');
            $studentId = $this->requiredValue($_POST, 'student_id');
            $email = $this->requiredValue($_POST, 'email');

            if ($name === '' || $grade === '' || $studentId === '' || $email === '') {
                $this->redirectTo('/students', ['error' => '未入力の項目があります']);
            }

            try {
                Student::create([
                    'name' => $name,
                    'grade' => $grade,
                    'student_id' => $studentId,
                    'email' => $email,
                ]);
            } catch (Throwable $exception) {
                $this->redirectTo('/students', ['error' => '学生を登録できませんでした']);
            }

            $this->redirectTo('/students', ['message' => '学生を登録しました']);
        }

        $this->render('students', ['students' => Student::all()]);
    }

    public function loans(array $params = []): void
    {
        $showOverdue = ($_GET['status'] ?? '') === 'overdue';

        $this->render('loans', [
            'showOverdue' => $showOverdue,
            'loans' => $showOverdue ? Loan::overdue() : Loan::all(),
        ]);
    }

    public function adminBooks(array $params = []): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $this->requiredValue($_POST, 'action');

            if ($action === 'create') {
                $this->createBook();
            }

            if ($action === 'delete') {
                $this->deleteBook();
            }
        }

        $this->render('admin-books', ['books' => Book::all()]);
    }

    private function createBook(): void
    {
        $title = $this->requiredValue($_POST, 'title');
        $barcode = $this->requiredValue($_POST, 'barcode');
        $location = $this->requiredValue($_POST, 'location');

        if ($title === '' || $barcode === '' || $location === '') {
            $this->redirectTo('/admin-books', ['error' => 'タイトル、バーコード、場所は必須です']);
        }

        try {
            Book::create([
                'title' => $title,
                'author' => $this->requiredValue($_POST, 'author') ?: null,
                'isbn' => $this->requiredValue($_POST, 'isbn') ?: null,
                'barcode' => $barcode,
                'location' => $location,
                'status' => $this->requiredValue($_POST, 'status') ?: 'available',
            ]);
        } catch (Throwable $exception) {
            $this->redirectTo('/admin-books', ['error' => '本を登録できませんでした']);
        }

        $this->redirectTo('/admin-books', ['message' => '本を登録しました']);
    }

    private function deleteBook(): void
    {
        $bookId = (int) $this->requiredValue($_POST, 'book_id');

        try {
            $deleted = $bookId > 0 && Book::delete($bookId);
        } catch (Throwable $exception) {
            $deleted = false;
        }

        if (!$deleted) {
            $this->redirectTo('/admin-books', ['error' => '本を削除できませんでした']);
        }

        $this->redirectTo('/admin-books', ['message' => '本を削除しました']);
    }

    private function redirectTo(string $path, array $params = []): void
    {
        $query = $params === [] ? '' : '?' . http_build_query($params);

        header('Location: ' . $path . $query);
        exit;
    }

    private function requiredValue(array $data, string $key): string
    {
        return trim((string) ($data[$key] ?? ''));
    }
}
