<?php
declare(strict_types=1);

use App\Models\Book;

require_once __DIR__ . '/_view_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = requiredValue($_POST, 'action');

    if ($action === 'create') {
        $title = requiredValue($_POST, 'title');
        $barcode = requiredValue($_POST, 'barcode');
        $location = requiredValue($_POST, 'location');

        if ($title === '' || $barcode === '' || $location === '') {
            redirectTo('admin-books.php', ['error' => 'タイトル、バーコード、場所は必須です']);
        }

        try {
            Book::create([
                'title' => $title,
                'author' => requiredValue($_POST, 'author') ?: null,
                'isbn' => requiredValue($_POST, 'isbn') ?: null,
                'barcode' => $barcode,
                'location' => $location,
                'status' => requiredValue($_POST, 'status') ?: 'available',
            ]);
        } catch (\Throwable $exception) {
            redirectTo('admin-books.php', ['error' => '本を登録できませんでした']);
        }

        redirectTo('admin-books.php', ['message' => '本を登録しました']);
    }

    if ($action === 'delete') {
        $bookId = (int) requiredValue($_POST, 'book_id');

        try {
            $deleted = $bookId > 0 && Book::delete($bookId);
        } catch (\Throwable $exception) {
            $deleted = false;
        }

        if (!$deleted) {
            redirectTo('admin-books.php', ['error' => '本を削除できませんでした']);
        }

        redirectTo('admin-books.php', ['message' => '本を削除しました']);
    }
}

$books = Book::all();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>本の管理</title>
</head>
<body>
    <header>
        <h1>本の管理</h1>
        <p><a href="home.php">トップへ戻る</a></p>
    </header>

    <?php renderMessage(); ?>

    <section>
        <h2>本を登録する</h2>
        <form action="admin-books.php" method="post">
            <input type="hidden" name="action" value="create">

            <p>
                <label>
                    タイトル
                    <input type="text" name="title" required>
                </label>
            </p>

            <p>
                <label>
                    著者
                    <input type="text" name="author">
                </label>
            </p>

            <p>
                <label>
                    ISBN
                    <input type="text" name="isbn">
                </label>
            </p>

            <p>
                <label>
                    バーコード
                    <input type="text" name="barcode" required>
                </label>
            </p>

            <p>
                <label>
                    場所
                    <input type="text" name="location" required>
                </label>
            </p>

            <p>
                <label>
                    状態
                    <select name="status">
                        <option value="available">貸し出し可能</option>
                        <option value="borrowed">貸し出し中</option>
                    </select>
                </label>
            </p>

            <button type="submit">登録</button>
        </form>
    </section>

    <section>
        <h2>登録済みの本</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>タイトル</th>
                    <th>著者</th>
                    <th>バーコード</th>
                    <th>場所</th>
                    <th>状態</th>
                    <th>操作</th>
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
                        <td><?= h($book['barcode']) ?></td>
                        <td><?= h($book['location']) ?></td>
                        <td><?= h($book['status']) ?></td>
                        <td>
                            <form action="admin-books.php" method="post">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="book_id" value="<?= h((string) $book['id']) ?>">
                                <button type="submit">削除</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</body>
</html>
