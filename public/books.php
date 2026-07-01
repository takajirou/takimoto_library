<?php
declare(strict_types=1);

use App\Models\Book;

require_once __DIR__ . '/_view_bootstrap.php';

$books = Book::all([
    'status' => $_GET['status'] ?? '',
    'keyword' => $_GET['keyword'] ?? '',
]);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>本一覧</title>
</head>
<body>
    <header>
        <h1>本一覧</h1>
        <p><a href="home.php">トップへ戻る</a></p>
    </header>

    <?php renderMessage(); ?>

    <section>
        <h2>検索</h2>
        <form action="books.php" method="get">
            <label>
                キーワード
                <input type="text" name="keyword" value="<?= h($_GET['keyword'] ?? '') ?>">
            </label>

            <label>
                状態
                <select name="status">
                    <option value="">すべて</option>
                    <option value="available" <?= ($_GET['status'] ?? '') === 'available' ? 'selected' : '' ?>>貸し出し可能</option>
                    <option value="borrowed" <?= ($_GET['status'] ?? '') === 'borrowed' ? 'selected' : '' ?>>貸し出し中</option>
                </select>
            </label>

            <button type="submit">検索</button>
        </form>
    </section>

    <section>
        <h2>本の情報</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>タイトル</th>
                    <th>著者</th>
                    <th>ISBN</th>
                    <th>バーコード</th>
                    <th>場所</th>
                    <th>状態</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($books === []): ?>
                    <tr>
                        <td colspan="7">本がありません。</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?= h((string) $book['id']) ?></td>
                        <td><?= h($book['title']) ?></td>
                        <td><?= h($book['author'] ?? '') ?></td>
                        <td><?= h($book['isbn'] ?? '') ?></td>
                        <td><?= h($book['barcode']) ?></td>
                        <td><?= h($book['location']) ?></td>
                        <td><?= h($book['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</body>
</html>
