<?php
declare(strict_types=1);

use App\Models\Book;
use App\Models\Loan;
use App\Models\Student;

require_once __DIR__ . '/_view_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = (int) requiredValue($_POST, 'student_id');
    $bookId = (int) requiredValue($_POST, 'book_id');

    if ($studentId <= 0 || $bookId <= 0) {
        redirectTo('borrow.php', ['error' => '学生と本を選択してください']);
    }

    $result = Loan::borrow($studentId, $bookId);

    if (!$result['success']) {
        redirectTo('borrow.php', ['error' => $result['message']]);
    }

    redirectTo('borrow.php', ['message' => '本を貸し出しました']);
}

$students = Student::all();
$books = Book::all(['status' => 'available']);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>貸し出し</title>
</head>
<body>
    <header>
        <h1>貸し出し</h1>
        <p><a href="home.php">トップへ戻る</a></p>
    </header>

    <?php renderMessage(); ?>

    <section>
        <h2>本を借りる</h2>
        <form action="borrow.php" method="post">
            <p>
                <label>
                    学生
                    <select name="student_id" required>
                        <option value="">選択してください</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?= h((string) $student['id']) ?>">
                                <?= h($student['name']) ?>（<?= h((string) $student['student_id']) ?>）
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </p>

            <p>
                <label>
                    本
                    <select name="book_id" required>
                        <option value="">選択してください</option>
                        <?php foreach ($books as $book): ?>
                            <option value="<?= h((string) $book['id']) ?>">
                                <?= h($book['title']) ?>（<?= h($book['barcode']) ?>）
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </p>

            <button type="submit">貸し出し</button>
        </form>
    </section>

    <section>
        <h2>貸し出しルール</h2>
        <ul>
            <li>貸出期間は2週間</li>
            <li>1人が同時に借りられる冊数は3冊まで</li>
            <li>返却期限を過ぎた場合は延滞</li>
        </ul>
    </section>
</body>
</html>
