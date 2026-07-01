<?php
declare(strict_types=1);

use App\Models\Loan;

require_once __DIR__ . '/_view_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loanId = (int) requiredValue($_POST, 'loan_id');

    if ($loanId <= 0) {
        redirectTo('return.php', ['error' => '貸し出し情報を選択してください']);
    }

    $result = Loan::returnBook($loanId);

    if (!$result['success']) {
        redirectTo('return.php', ['error' => $result['message']]);
    }

    redirectTo('return.php', ['message' => '本を返却しました']);
}

$loans = array_filter(
    Loan::all(),
    fn (array $loan): bool => $loan['status'] === 'borrowed'
);
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
        <p><a href="home.php">トップへ戻る</a></p>
    </header>

    <?php renderMessage(); ?>

    <section>
        <h2>本を返却する</h2>
        <form action="return.php" method="post">
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
