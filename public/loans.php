<?php
declare(strict_types=1);

use App\Models\Loan;

require_once __DIR__ . '/_view_bootstrap.php';

$showOverdue = ($_GET['status'] ?? '') === 'overdue';
$loans = $showOverdue ? Loan::overdue() : Loan::all();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>貸し出し履歴</title>
</head>
<body>
    <header>
        <h1>貸し出し履歴</h1>
        <p><a href="home.php">トップへ戻る</a></p>
    </header>

    <nav>
        <ul>
            <li><a href="loans.php">すべて</a></li>
            <li><a href="loans.php?status=overdue">延滞中のみ</a></li>
        </ul>
    </nav>

    <section>
        <h2><?= $showOverdue ? '延滞中の本' : '履歴一覧' ?></h2>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>学生</th>
                    <th>学籍番号</th>
                    <th>本</th>
                    <th>バーコード</th>
                    <th>貸出日</th>
                    <th>返却期限</th>
                    <th>返却日</th>
                    <th>状態</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($loans === []): ?>
                    <tr>
                        <td colspan="9">貸し出し履歴がありません。</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($loans as $loan): ?>
                    <tr>
                        <td><?= h((string) $loan['id']) ?></td>
                        <td><?= h($loan['student_name']) ?></td>
                        <td><?= h((string) $loan['student_number']) ?></td>
                        <td><?= h($loan['book_title']) ?></td>
                        <td><?= h($loan['book_barcode']) ?></td>
                        <td><?= h($loan['borrowed_at']) ?></td>
                        <td><?= h($loan['due_date']) ?></td>
                        <td><?= h($loan['returned_at'] ?? '') ?></td>
                        <td><?= h($loan['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</body>
</html>
