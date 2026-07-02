<?php
declare(strict_types=1);

require_once __DIR__ . '/_helpers.php';
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
        <p><a href="/">トップへ戻る</a></p>
    </header>

    <?php renderMessage(); ?>

    <section>
        <h2>本を借りる</h2>
        <form action="/borrow" method="post">
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
