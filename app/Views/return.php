<?php
declare(strict_types=1);

require_once __DIR__ . '/_helpers.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>返却</title>
</head>
<body>
    <header>
        <h1>返却</h1>
        <p><a href="/">トップへ戻る</a></p>
    </header>

    <?php renderMessage(); ?>

    <section>
        <h2>本を返却する</h2>
        <form action="/return" method="post">
            <p>
                <label>
                    貸し出し情報
                    <select name="loan_id" required>
                        <option value="">選択してください</option>
                        <?php foreach ($loans as $loan): ?>
                            <option value="<?= h((string) $loan['id']) ?>">
                                <?= h($loan['student_name']) ?> / <?= h($loan['book_title']) ?> / 期限: <?= h($loan['due_date']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </p>

            <button type="submit">返却</button>
        </form>
    </section>
</body>
</html>
