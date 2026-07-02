<?php
declare(strict_types=1);

require_once __DIR__ . '/_helpers.php';
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
        <p><a href="/">トップへ戻る</a></p>
    </header>

    <?php renderMessage(); ?>

    <section>
        <h2>本を登録する</h2>
        <form action="/admin-books" method="post">
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
                            <form action="/admin-books" method="post">
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
