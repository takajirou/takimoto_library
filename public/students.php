<?php
declare(strict_types=1);

use App\Models\Student;

require_once __DIR__ . '/_view_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = requiredValue($_POST, 'name');
    $grade = requiredValue($_POST, 'grade');
    $studentId = requiredValue($_POST, 'student_id');
    $email = requiredValue($_POST, 'email');

    if ($name === '' || $grade === '' || $studentId === '' || $email === '') {
        redirectTo('students.php', ['error' => '未入力の項目があります']);
    }

    try {
        Student::create([
            'name' => $name,
            'grade' => $grade,
            'student_id' => $studentId,
            'email' => $email,
        ]);
    } catch (\Throwable $exception) {
        redirectTo('students.php', ['error' => '学生を登録できませんでした']);
    }

    redirectTo('students.php', ['message' => '学生を登録しました']);
}

$students = Student::all();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>学生管理</title>
</head>
<body>
    <header>
        <h1>学生管理</h1>
        <p><a href="home.php">トップへ戻る</a></p>
    </header>

    <?php renderMessage(); ?>

    <section>
        <h2>学生登録</h2>
        <form action="students.php" method="post">
            <p>
                <label>
                    名前
                    <input type="text" name="name" required>
                </label>
            </p>

            <p>
                <label>
                    学年
                    <input type="number" name="grade" required>
                </label>
            </p>

            <p>
                <label>
                    学籍番号
                    <input type="number" name="student_id" required>
                </label>
            </p>

            <p>
                <label>
                    メールアドレス
                    <input type="email" name="email" required>
                </label>
            </p>

            <button type="submit">登録</button>
        </form>
    </section>

    <section>
        <h2>学生一覧</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>名前</th>
                    <th>学年</th>
                    <th>学籍番号</th>
                    <th>メールアドレス</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($students === []): ?>
                    <tr>
                        <td colspan="5">学生がいません。</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= h((string) $student['id']) ?></td>
                        <td><?= h($student['name']) ?></td>
                        <td><?= h((string) $student['grade']) ?></td>
                        <td><?= h((string) $student['student_id']) ?></td>
                        <td><?= h($student['email']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</body>
</html>
